<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Data;

class logsController extends Controller
{
    public function index()
    {
        $logs = Data::all()->sortByDesc('ID');
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

}

