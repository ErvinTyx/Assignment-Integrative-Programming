<?php

namespace App\Http\Controllers;

use App\Enums\RolesEnum;
use App\Enums\VendorStatusEnum;
use App\Http\Resources\ProductListResource;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function profile(Request $request, Vendor $vendor)
    {
        $useApi = $request->query('use_api', false);
        $products = [];

        try {
            if ($useApi) {
                // External API consumption (Product Module REST API)
                $response = Http::timeout(10)->get(route('api.products.by-vendor', [
                    'vendor_id' => $vendor->user_id,
                ]));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch products via API');
                }

                $products = collect($response->json()['data'] ?? []);
            } else {
                // Internal DB query
                $products = \App\Models\Product::query()
                    ->forWebsite()
                    ->where('created_by', $vendor->user_id)
                    ->get();
            }
        } catch (\Exception $e) {
            return Inertia::render('Vendor/Profile', [
                'vendor' => $vendor,
                'products' => collect([]),
                'error' => $e->getMessage(),
            ]);
        }

        return Inertia::render('Vendor/Profile', [
            'vendor' => $vendor,
            'products' => ProductListResource::collection($products),
        ]);
    }

    public function store(Request $request)
    {
        
        $user = $request->user();

        $request->validate([
           'store_name' => ['required', "regex:/^[a-z0-9-]+$/", Rule::unique('vendors', 'store_name')->ignore($user->id, 'user_id')],
           'store_address' => 'nullable',
        ], [
            'store_name.regex' => 'Only lowercase letters, numbers and hyphens are allowed.',
        ]);

        $vendor = $user->vendor?: new Vendor();
        $vendor->user_id = $user->id;
        $vendor->store_name = $request->store_name;
        $vendor->store_address = $request->store_address;
        $vendor->status = VendorStatusEnum::Approved->value;
        $vendor->save();
        $user->assignRole(RolesEnum::Vendor);

        return redirect()->route('vendor.profile', $vendor->store_name);
    }
}
