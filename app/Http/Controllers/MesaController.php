<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::all();
        return Response::json($mesas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'propietario' => 'required|integer',
            'indicaciones' => 'required|string',
        ], [
            'propietario.required' => 'El campo propietario es obligatorio.',
            'indicaciones.required' => 'El campo indicaciones es obligatorio.',
        ]);

        if ($validator->fails()) {
            return Response::json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $mesa = new Mesa($validatedData);
        $mesa->save();

        return Response::json($mesa, 201);
    }

    public function show($id)
    {
        $mesa = Mesa::findOrFail($id);
        return Response::json($mesa);
    }

    public function update(Request $request, $id)
    {
        $mesa = Mesa::findOrFail($id);
        $validatedData = $request->validate([
            'indicaciones' => 'nullable|string',
        ]);

        $mesa->update($validatedData);
        return Response::json($mesa);
    }

    public function destroy($id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->delete();
        return Response::json(['message' => 'Mesa eliminada exitosamente.']);
    }
}