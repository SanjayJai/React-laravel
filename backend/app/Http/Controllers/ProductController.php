<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Assuming you have a Product model

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = auth()->id();

        $products = Product::where('user_id', $user_id)->get();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'products' => $products,
        
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $data = $request->validate([
        'title'=>'required|string|max:255',
    ]);

    $data['user_id'] = auth()->id();
    if ($request->hasFile('banner_image')) {
        $data['banner_image'] = $request->file('banner_image')->store('products', 'public');
    }

    $product = Product::create($data);

    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product,  
    ], 201);
   
}

/**
 * Display the specified resource.
 */
public function show(Product $product)
{
    // Ensure the product belongs to the authenticated user
    if ($product->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json([
        'status' => true,
        'message' => 'Product retrieved successfully',
        'product' => $product,
    ]);
}
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Ensure the product belongs to the authenticated user
        if ($product->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'message' => 'Product retrieved successfully',
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Ensure the product belongs to the authenticated user
        if ($product->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
           
        ]);
        if ($request->hasFile('banner_image')) {
            if($product->banner_image) {
                // Delete the old image if it exists
                Storage::disk('public')->delete($product->banner_image);

            }

            $data['banner_image'] = $request->file('banner_image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Ensure the product belongs to the authenticated user
        if ($product->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ]); 
    }
}
