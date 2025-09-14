<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorApiController extends Controller
{
    /**
     * List all vendors
     */
    public function index()
    {
        $vendors = Vendor::all(['id', 'user_id', 'store_name', 'store_address', 'status']);
        return response()->json($vendors);
    }

    /**
     * Show a specific vendor
     */
    public function show(Vendor $vendor)
    {
        return response()->json($vendor);
    }

    /**
     * Create a new vendor
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_name' => 'required|string|unique:vendors,store_name',
            'store_address' => 'nullable|string',
        ]);

        $vendor = Vendor::create([
            'user_id' => $request->user_id,
            'store_name' => $request->store_name,
            'store_address' => $request->store_address,
            'status' => 'approved', // default status
        ]);

        return response()->json($vendor, 201);
    }

    /**
     * Update a vendor
     */
    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'store_name' => 'sometimes|required|string|unique:vendors,store_name,' . $vendor->id,
            'store_address' => 'sometimes|nullable|string',
            'status' => 'sometimes|string',
        ]);

        $vendor->update($request->only(['store_name', 'store_address', 'status']));

        return response()->json($vendor);
    }

    /**
     * Delete a vendor
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return response()->json(['success' => true]);
    }
}
