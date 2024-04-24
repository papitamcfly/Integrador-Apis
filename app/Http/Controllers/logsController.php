<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Data;
use App\Models\meseros;
use App\Models\sensores;

class logsController extends Controller
{
    public function index()
    {
        $logs = Data::all();
        return response()->json($logs);
    }

    public function showRecent($mesero)
    {
        $logsbyrobot = Data::raw(function ($collection) use ($mesero) {
            return $collection->aggregate([
                ['$match' => ['meseroID' => (int)$mesero]], // Filtrar por meseroID
                ['$sort' => ['ID' => -1]], // Más recientes primero
                [
                    '$group' => [
                        '_id' => '$identificador',
                        'data' => ['$first' => '$$ROOT']
                    ]
                ]
            ]);
        });
        return response()->json($logsbyrobot);
    }
    public function MostrarMeseros(){
        $meseros = meseros::all();
        return response()->json($meseros);
    }
    public function MostrarSensores(){
        $meseros = sensores::all();
        return response()->json($meseros);
    }
}

