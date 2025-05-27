<?php

namespace App\Http\Controllers;

use App\Models\Acceso;
use Illuminate\Http\Request;

class AccesoController extends Controller
{
    public function index(Request $request) {
        $usuario_id = $request->get('user_id');
        $ini = $request->get('fecha_inicio');
        $fin = $request->get('fecha_fin');
        if (!$ini || !$fin) {
            $ini = date('Y-m-d');
            $fin = date('Y-m-d');
        } else {
            $ini = date('Y-m-d',strtotime($ini));
            $fin = date('Y-m-d',strtotime($fin));
        }
        $data = Acceso::susuario($usuario_id,'')
        ->sfechas($ini,$fin,'')
        ->latest()->with(['user'])->get();
        foreach ($data as $key => $value) {
            $value->txt_data = $value->user->nombre_completo;
        }
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Acceso::find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        $data = [
            'success' => 'Registrado correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function update(Request $request, $id) {
        $data = [
            'success' => 'Registro actualizado correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $newItem = Acceso::find($id);
        if (!$newItem) {
            return response()->json('No encontrado.', 409);
        }
        // $valid = Producto::where('marca_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado.', 409);
        // }
        // $newItem->delete();
        $data = [
            'success' => 'Registro eliminado correctamente.'
        ];
        return response()->json($data, 200);
    }
}
