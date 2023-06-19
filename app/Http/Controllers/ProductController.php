<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::when(request('search') , function($query){
            $query->where('name' , 'like' , '%' . request('search') . '%')
                    ->orWhere('price' , 'like' , '%' . request('search') . '%');
        })->latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCreateRequest $request)
    {
        Product::create($request->only('name' , 'price'));
        return response()->json(['message' => 'Product Create Successfully'] , 200);
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
        $product->update($request->only('name' , 'price'));
        return response()->json(['message' => 'Product update successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product delete successfully']);
    }
}
