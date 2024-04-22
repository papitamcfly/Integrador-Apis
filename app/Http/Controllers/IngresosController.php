<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;

class IngresosController extends Controller
{
    public function getIngresos($type){
        $pipeline = [];
        $fecha = Carbon::now('America/Mexico_City');
        if($type == "PorDia"){
            $pipeline = [
                ['$group' => [
                    '_id' => $fecha,
                    'totalIngresos' => ['$sum' => '$total'],
                ]]
            ];
        }
        else if ($type == 'Por Semana') {
            $pipeline = [
                ['$addFields' => [
                    'semana' => ['$dateToString' => ['format' => '%Y-%U', 'date' => $fecha]]
                ]],
                ['$group' => [
                    '_id' => '$semana',
                    'totalIngresos' => ['$sum' => '$total']
                ]],
            ];
        }
        else if($type == 'PorMes'){
            $pipeline = [
                ['$addFields' => [
                    'mes' => ['$substr' => [$fecha, 0, 7]]
                ]],
                ['$group' => [
                    '_id' => '$mes',
                    'totalIngresos' => ['$sum' => '$total']
                ]]
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
                    '_id' => ['fecha' => $fecha, 'producto' => '$producto.name'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
                ['$group' => [
                    '_id' => '$_id.fecha',
                    'productos' => [
                        '$push' => [
                            'producto' => '$_id.producto',
                            'totalIngresos' => '$totalIngresos'
                        ]
                    ],
                ]],
            ];
        }
        else if ($type == 'Por Semana') {
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
                    'semana' => ['$dateToString' => ['format' => '%Y-%U', 'date' => $fecha]]
                ]],
                ['$group' => [
                    '_id' => ['semana' => '$semana', 'producto' => '$producto.name'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
                ['$group' => [
                    '_id' => '$_id.fecha',
                    'productos' => [
                        '$push' => [
                            'producto' => '$_id.producto',
                            'totalIngresos' => '$totalIngresos'
                        ]
                    ],
                ]],
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
                    'mes' => ['$substr' => ['$fecha', 0, 7]]
                ]],
                ['$group' => [
                    '_id' => ['mes' => '$mes', 'producto' => '$producto.name'],
                    'totalIngresos' => ['$sum' => ['$multiply' => ['$detalles.quantity', '$producto.price']]]
                ]],
            ];
        }
        $result = Order::raw(function ($collection) use ($pipeline) {
            return $collection->aggregate($pipeline);
        });
    
        return response()->json($result);
    }
}