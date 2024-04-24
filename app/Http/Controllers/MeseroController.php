<?php

namespace App\Http\Controllers;

use App\Models\meseros;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MeseroController extends Controller
{
    public function index()
    {
        $meseros = meseros::all();
        return response()->json($meseros);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lastMesero = meseros::orderBy('id', 'desc')->first();
        $nextMeseroId = $lastMesero ? $lastMesero->id + 1 : 1;

        $mesero = new meseros();
        $mesero->Id = $nextMeseroId;
        $mesero->Nombre = $request->nombre;
        $mesero->UsuarioID = Auth::id();
        $mesero->save();

        return response()->json($mesero, 201);
    }

    public function show($id)
    {
        $mesero = meseros::findOrFail($id);
        return response()->json($mesero);
    }

    public function update(Request $request, $id)
    {
        $mesero = meseros::findOrFail($id);

        $validatedData = $request->validate([
            'nombre' => 'nullable|string',
        ]);

        $mesero->update($validatedData);

        return response()->json($mesero);
    }

    public function destroy($id)
    {
        $mesero = meseros::findOrFail($id);
        $mesero->delete();

        return response()->json(['message' => 'Mesero eliminado exitosamente.']);
    }
}