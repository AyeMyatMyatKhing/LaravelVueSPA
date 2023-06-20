<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        $priceRange = request('price');
        try {
            $productList = Product::when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($priceRange, function ($query) use ($priceRange) {
                $query->whereBetween('price', $priceRange);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

            $priceList = Product::pluck('price');

            $categoryProduct = Product::get();
            
            return response()->json(['product' => $productList , 'price' => $priceList , 'categoryProduct' => $categoryProduct]);
        } catch(\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); 
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCreateRequest $request)
    {
        try {
            Log::info($request->only('name' , 'price' , 'category'));
            Product::create($request->only('name' , 'price' , 'category'));
            return response()->json(['message' => 'Product Create Successfully'] , 200);
        } catch(\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); 
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
       return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            $product->update($request->only('name' , 'price' , 'category'));
            return response()->json(['message' => 'Product update successfully']);
        } catch(\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return response()->json(['message' => 'Product delete successfully']);
        } catch(\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); 
        }
        
    }
}
