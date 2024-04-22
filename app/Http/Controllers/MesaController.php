<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::all();
        return Response::json($mesas);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|unique:mesas',
            'propietario' => 'required|integer',
            'indicaciones' => 'nullable|string',
        ]);

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
            'propietario' => 'required|integer',
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