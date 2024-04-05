<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $order = new Order();
        $order->customer_id = Auth::id();
        $order->save();

        foreach ($request->items as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->save();
        }

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ]);
    }
}
