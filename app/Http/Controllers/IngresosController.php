<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;

class IngresosController extends Controller
{
    public function getIngresos($type){
        $pipeline = [];
        $fecha = Carbon::now('America/Mexico_City');
        Log::info($fecha);
        if($type == "PorDia"){
            $pipeline = [
                ['$lookup' => [
                    'from' => 'detalleorden',
                    'localField' => 'id',
                    'foreignField' => 'order_id',
                    'as' => 'detalles'
                ]],
                ['$unwind' => '$detalles'],
                ['$lookup' => [
                    'from' => 'productos',
                    'localField' => 'detalles.product_id',
                    'foreignField' => 'id',
                    'as' => 'producto'
                ]],
                ['$unwind' => '$producto'],
                ['$group' => [
                    '_id' => ['fecha' => '$fecha'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
                ['$sort' => ['_id' => -1]]
            ];
        }
        else if ($type == 'PorSemana') {
            $pipeline = [
                ['$lookup' => [
                    'from' => 'detalleorden',
                    'localField' => 'id',
                    'foreignField' => 'order_id',
                    'as' => 'detalles'
                ]],
                ['$unwind' => '$detalles'],
                ['$lookup' => [
                    'from' => 'productos',
                    'localField' => 'detalles.product_id',
                    'foreignField' => 'id',
                    'as' => 'producto'
                ]],
                ['$unwind' => '$producto'],
                ['$addFields' => [
                    'fecha' => ['$toDate' => '$fecha']
                ]],
                ['$addFields' => [
                    'semana' => ['$dateToString' => ['format' => '%U week %Y', 'date' => '$fecha']]
                ]],
                ['$group' => [
                    '_id' => ['semana' => '$semana'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
                ['$sort' => ['_id' => -1]]
            ];
        }
        else if($type == 'PorMes'){
            $pipeline = [
                ['$lookup' => [
                    'from' => 'detalleorden',
                    'localField' => 'id',
                    'foreignField' => 'order_id',
                    'as' => 'detalles'
                ]],
                ['$unwind' => '$detalles'],
                ['$lookup' => [
                    'from' => 'productos',
                    'localField' => 'detalles.product_id',
                    'foreignField' => 'id',
                    'as' => 'producto'
                ]],
                ['$unwind' => '$producto'],
                ['$addFields' => [
                    'fecha' => ['$toDate' => '$fecha']
                ]],
                ['$addFields' => [
                    'mes' => ['$substr' => ['$fecha', 0, 7]]
                ]],
                ['$group' => [
                    '_id' => ['mes' => '$mes'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
                ['$sort' => ['_id' => -1]]
            ];
        }
        $result = Order::raw(function ($collection) use ($pipeline) {
            return $collection->aggregate($pipeline);
        });
    
        return response()->json($result);
    }
    public function getIngresosProductos($type){
        $pipeline = [];
        $fecha = Carbon::now('America/Mexico_City');
        if($type == "PorDia"){
            $pipeline =[
                ['$lookup' => [
                    'from' => 'detalleorden',
                    'localField' => 'id',
                    'foreignField' => 'order_id',
                    'as' => 'detalles'
                ]],
                ['$unwind' => '$detalles'],
                ['$lookup' => [
                    'from' => 'productos',
                    'localField' => 'detalles.product_id',
                    'foreignField' => 'id',
                    'as' => 'producto'
                ]],
                ['$unwind' => '$producto'],

                ['$group' => [
                    '_id' => ['fecha' => '$fecha', 'producto' => '$producto.name'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
                ['$sort' => ['totalIngresos' => -1]],
                ['$group' => [
                    '_id' => '$_id.fecha',
                    'productos' => [
                        '$push' => [
                            'producto' => '$_id.producto',
                            'totalIngresos' => '$totalIngresos'
                        ]
                    ],
                ]],
                ['$sort' => ['_id' => -1]]
            ];
        }
        else if ($type == 'PorSemana') {
            $pipeline = [
                ['$lookup' => [
                    'from' => 'detalleorden',
                    'localField' => 'id',
                    'foreignField' => 'order_id',
                    'as' => 'detalles'
                ]],
                ['$unwind' => '$detalles'],
                ['$lookup' => [
                    'from' => 'productos',
                    'localField' => 'detalles.product_id',
                    'foreignField' => 'id',
                    'as' => 'producto'
                ]],
                ['$unwind' => '$producto'],
                ['$addFields' => [
                    'fecha' => ['$toDate' => '$fecha']
                ]],
                ['$addFields' => [
                    'semana' => ['$dateToString' => ['format' => '%U week %Y', 'date' => '$fecha']]
                ]],
                ['$group' => [
                    '_id' => ['semana' => '$semana', 'producto' => '$producto.name'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
                ['$sort' => ['totalIngresos' => -1]],
                ['$group' => [
                    '_id' => '$_id.semana',
                    'productos' => [
                        '$push' => [
                            'producto' => '$_id.producto',
                            'totalIngresos' => '$totalIngresos'
                        ]
                    ],
                ]],
                ['$sort' => ['_id' => -1]]
            ];
        }
        else if($type == 'PorMes'){
            $pipeline = [
                ['$lookup' => [
                    'from' => 'detalleorden',
                    'localField' => 'id',
                    'foreignField' => 'order_id',
                    'as' => 'detalles'
                ]],
                ['$unwind' => '$detalles'],
                ['$lookup' => [
                    'from' => 'productos',
                    'localField' => 'detalles.product_id',
                    'foreignField' => 'id',
                    'as' => 'producto'
                ]],
                ['$unwind' => '$producto'],
                ['$addFields' => [
                    'fecha' => ['$toDate' => '$fecha']
                ]],
                ['$addFields' => [
                    'mes' => ['$substr' => ['$fecha', 0, 7]]
                ]],
                ['$group' => [
                    '_id' => ['mes' => '$mes', 'producto' => '$producto.name'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
                ['$sort' => ['totalIngresos' => -1]],
                ['$group' => [
                    '_id' => '$_id.mes',
                    'productos' => [
                        '$push' => [
                            'producto' => '$_id.producto',
                            'totalIngresos' => '$totalIngresos'
                        ]
                    ],
                ]],
                ['$sort' => ['_id' => -1]]
            ];
        }
        $result = Order::raw(function ($collection) use ($pipeline) {
            return $collection->aggregate($pipeline);
        });
    
        return response()->json($result);
    }
}