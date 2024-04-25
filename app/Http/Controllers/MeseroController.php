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
        $user = auth()->user();
        $userId = $user ? $user->id : null;
        $lastMesero = meseros::orderBy('Id', 'desc')->first();
        $nextMeseroId = $lastMesero ? $lastMesero->Id + 1 : 1;

        $mesero = new meseros();
        $mesero->Id = $nextMeseroId;
        $mesero->Nombre = $request->nombre;
        $mesero->UsuarioID = $userId;
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
        $id = intval($id);
        $mesero = meseros::where('Id', $id)->first();

        if (!$mesero) {
            return response()->json(['message' => 'Mesero no encontrado.'], 404);
        }

        $mesero->delete();

        return response()->json(['message' => 'Mesero eliminado exitosamente.']);
    }
}