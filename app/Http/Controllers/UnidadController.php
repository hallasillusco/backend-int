<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use App\Models\Producto;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index(Request $request) {
        $term = $request->get('term');
        $data = Unidad::orderBy('nombre')->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Unidad::find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        $valid = Unidad::where('nombre',$request->nombre)->first();
        if ($valid) {
            return response()->json('El nombre ya fue registrado.', 409);
        }
        $newData = new Unidad;
        $newData->nombre = $request->nombre;
        $newData->sigla = $request->sigla;
        $newData->habilitado = true;
        $newData->save();
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $editData = Unidad::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        $editData->nombre = $request->nombre;
        $editData->sigla = $request->sigla;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Unidad::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        $valid = Producto::where('unidad_id',$id)->first();
        if ($valid) {
            return response()->json('No puede ser eliminado', 409);
        }
        $data->delete();
        return response()->json($data, 200);
    }

    public function habilitar($id) {
        $item = Unidad::find($id);
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
        $data = Unidad::habilitado()->get();
        return response()->json($data, 200);
    }
}
