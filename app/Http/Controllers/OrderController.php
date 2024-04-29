<?php

namespace App\Http\Controllers;

use App\Events\GeneroActualizado;
use App\Events\OrdenPendiente;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $lastOrder = Order::orderBy('id', 'desc')->first();
        $nextOrderId = $lastOrder ? $lastOrder->id + 1 : 1;
    
     
        $currentDateTime = Carbon::now('America/Mexico_City');
    

        $order = new Order();
        $order->id = $nextOrderId; 
        $order->customer_id = Auth::id();
        $order->mesa_id = 0; 
        $order->status = "pendiente";
        $order->fecha = $currentDateTime->toDateString();
        $order->hora = $currentDateTime->toTimeString();
        $order->total = $request->total;
        $order->save();
    

        foreach ($request->items as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->save();
        }
        event(new GeneroActualizado($order));
        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ]);
    }
    public function index($estado)
    {
        $ordersWithItems = Order::raw(function ($collection) use ($estado) {
            return $collection->aggregate([
                [
                    '$match'=> [
                        'status'=> $estado 
                    ]
                ],
                [
                    '$lookup'=> [
                        'from'=>"detalleorden",
                        'localField'=> "id",
                        'foreignField'=> "order_id",
                        'as'=> "detalles"
                    ]
                ],
                [
                    '$lookup'=> [
                        'from'=>"productos",
                        'localField'=> "detalles.product_id",
                        'foreignField'=> "id",
                        'as'=> "productos"
                    ]
                ],
                [
                    '$sort'=> ['fecha'=> -1,'hora'=> -1] 
                ],
                [
                    '$group'=> [
                        '_id'=> '$status',
                        'ordenes'=>[
                            '$push'=> '$$ROOT'
                        ]
                    ]
                ]
            ]);
        });
    
        return $ordersWithItems;
    }
    function changestatus($id,$estado)
    {
        $id = intval($id);
        $order = Order::where('id', $id)->first();



        if (!$order) {
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

  
        $order->status = $estado;
        $order->save();
        event(new GeneroActualizado($order));

        return response()->json(['message' => 'Estado de la orden actualizado correctamente'], 200);
    }
    function mailClient(Request $request){
        $date = Carbon::now('America/Mexico_City');
        $items = $request->input('items');
        $clientEmail = $request->input('clientEmail');
        $total = 0;
        $products = [];
        foreach ($items as $item) {
            $product = Product::where('id', $item['product_id'])->first(); // Assuming 'Product' is your model
            if ($product) {
                Log::info($product);
                $price = $product->price * $item['quantity'];
                $products[] = [
                    'name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $price, // Example: Fetching price as well
                ];
                $total += $price;
            }
        }
        Log::info($date);
        Log::info($items);
        Log::info($clientEmail);
        Mail::to($clientEmail)->send(new SendEmail($date, $products, $total));
    }
    
}
