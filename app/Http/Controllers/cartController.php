<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart;
class cartController extends Controller
{
    public function shop()
    {
        $products = Product::all();
       dd($products);
       return response()->json($products);
    }

    public function cart()
    {
        $cartCollection = Cart::getContent();
        return response()->json($cartCollection);
    }
    
    public function remove(Request $request)
    {
        Cart::remove($request->id);
        return response()->json(['message' => 'Item is removed!']);
    }
    
    public function add(Request $request)
    {
        Cart::add([
            'id' => $request->id,
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'attributes' => [
                'image' => $request->img,
                'slug' => $request->slug
            ]
        ]);
        return response()->json(['message' => 'Item added to cart!']);
    }
    
    public function update(Request $request)
    {
        Cart::update($request->id, [
            'quantity' => [
                'relative' => false,
                'value' => $request->quantity
            ],
        ]);
        return response()->json(['message' => 'Cart is updated!']);
    }
    
    public function clear()
    {
        Cart::clear();
        return response()->json(['message' => 'Cart is cleared!']);
    }

}
