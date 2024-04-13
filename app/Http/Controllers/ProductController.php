<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(){
        $products = Product::all();
        return response()->json($products);
    }

    public function show($id){
        $product = Product::find($id);
        if($product){
            return response()->json($product);
        }
        return response()->json(['message' => 'Product not found'], 404);
    }
    
    public function store(Request $request){
        if (!$request->hasFile('img')) {
            return response()->json(['errors' => ['img' => ['The img field is required.']]], 422);
        }
        $file = $request->file('img');
        $path = Storage::disk('s3')->put('products', $file);
        $url = Storage::disk('s3')->url($path); //Es normal el error, relaja las tetas Isaac
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:50',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $product = new Product();
        $product->description = $request->description;
        $product->id->id;
        $product->name = $request->name;
        $product->img->$url;
        $product->price = $request->price;
        $product->save();
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    

    public function update(Request $request, $id){
        $product = Product::find($id);
        if($product){
            if (!$request->hasFile('img')) {
                return response()->json(['errors' => ['img' => ['The img field is required.']]], 422);
            }
            $file = $request->file('img');
            $path = Storage::disk('s3')->put('products', $file);
            $url = Storage::disk('s3')->url($path);
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'description' => 'required|string|max:50',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $product->description = $request->description;
            $product->name = $request->name;
            $product->img->$url;
            $product->price = $request->price;
            $product->save();
            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product
            ]);
        }
        return response()->json(['message' => 'Product not found'], 404);
    }

    public function destroy($id){
        $product = Product::find($id);
        if($product){
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        }
        return response()->json(['message' => 'Product not found'], 404);
    }
}
