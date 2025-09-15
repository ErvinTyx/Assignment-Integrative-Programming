<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Get all products or products by vendor (external API)
     * GET /api/products or /api/products?vendor_id=1
     */
    public function index(Request $request)
    {
        try {
            $vendorId = $request->query('vendor_id');

            $query = Product::query()->forWebsite();

            if ($vendorId) {
                $query->where('created_by', $vendorId);
            }

            $products = $query->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get product details by ID
     * GET /api/products/{id}
     */
    /*public function show($id)
    {
        try {
            $product = Product::with(['variations', 'category'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'data' => null
            ], 404);
        }
    }*/
    public function show($id)
    {
        try {
            $product = Product::query()
                ->forWebsite()                 // published + vendorApproved()
                ->with(['variations', 'category'])
                ->select('products.*')         // because VendorApproved() uses a join
                ->findOrFail($id);

            return response()->json(['success' => true, 'data' => $product]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Product not found', 'data' => null], 404);
        }
    }


    /**
     * Create a new product
     * POST /api/products
     */
    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Product::class);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            // DO NOT accept created_by from client
        ]);

        $data['created_by'] = $request->user()->id;  // set on server
        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Update an existing product
     * PUT /api/products/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('update', $product);

            $data = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'quantity' => 'sometimes|nullable|integer|min:0',
                'category_id' => 'sometimes|nullable|exists:categories,id',
            ]);

            $product->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'data' => null
            ], 404);
        }
    }

    /**
     * Delete a product
     * DELETE /api/products/{id}
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('delete', $product);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }
}
