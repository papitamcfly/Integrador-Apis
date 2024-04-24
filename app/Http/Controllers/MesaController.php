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
        return response()->json($mesas);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $mesa = new Mesa($validatedData);
        $mesa->save();

        return response()->json($mesa, 201);
    }

    public function show($id)
    {
        $mesa = Mesa::findOrFail($id);
        return response()->json($mesa);
    }

    public function update(Request $request, $id)
    {
        $mesa = Mesa::findOrFail($id);
        $validatedData = $request->validate([
            'indicaciones' => 'nullable|string',
        ]);

        $mesa->update($validatedData);
        return response()->json($mesa);
    }

    public function destroy($id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->delete();
        return response()->json(['message' => 'Mesa eliminada exitosamente.']);
    }
}