<?php

namespace App\Http\Controllers;

use App\Models\Ayuda;
use Illuminate\Http\Request;

class AyudaController extends Controller
{
    public function index(Request $request) {
        $term = $request->get('term');
        $data = Ayuda::get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Ayuda::find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // no Ayudas repetidos
        $valid = Ayuda::where('nombre',$request->nombre)->first();
        if ($valid) {
            return response()->json('El nombre ya fue registrado.', 409);
        }
        $newData = new Ayuda;
        $newData->nombre = $request->nombre;
        $newData->descripcion = $request->descripcion;
        $newData->habilitado = true;
        $newData->save();
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $editData = Ayuda::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        $editData->nombre = $request->nombre;
        $editData->descripcion = $request->descripcion;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Ayuda::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        $data->delete();
        return response()->json($data, 200);
    }

    public function habilitar($id) {
        $item = Ayuda::find($id);
        $text = 'habilitado.';
        if ($item->habilitado) {
            $item->habilitado = false;
            $text = 'deshabilitado.';
        } else {
            $item->habilitado = true;
        }
        $item->save();
        return response()->json(['success' => 'Item '.$text], 200);
    }
    public function habilitados() {
        $data = Ayuda::habilitado()->get();
        return response()->json($data, 200);
    }
}
