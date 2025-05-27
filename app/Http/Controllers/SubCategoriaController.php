<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\SubCategoria;
use Illuminate\Http\Request;

class SubCategoriaController extends Controller
{
    public function index(Request $request) {
        $data = SubCategoria::with(['categoria'])->latest()->get();
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        $newItem = new SubCategoria;
        $newItem->nombre = $request->nombre;
        $newItem->categoria_id = $request->categoria_id;
        $newItem->habilitado = true;
        $newItem->save();
        $data = [
            'success' => 'Operacion realizada correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function update(Request $request, $id) {
        $newItem = SubCategoria::find($id);
        if (!$newItem) {
            return response()->json('No encontrado.', 409);
        }
        $newItem->nombre = $request->nombre;
        $newItem->categoria_id = $request->categoria_id;
        $newItem->save();
        $data = [
            'success' => 'Registro actualizado correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = SubCategoria::with(['categoria'])->find($id);
        return response()->json($data, 200);
    }
    public function showByCategoria($id) {
        $data = SubCategoria::where('categoria_id',$id)->get();
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $newItem = SubCategoria::find($id);
        if (!$newItem) {
            return response()->json('No encontrado.', 409);
        }
        $valid = Producto::where('sub_categoria_id',$id)->first();
        if ($valid) {
            return response()->json('No puede ser eliminado.', 409);
        }
        $newItem->delete();
        $data = [
            'success' => 'Registro eliminado correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function habilitar($id) {
        $item = SubCategoria::find($id);
        $text = 'habilitado.';
        if ($item->habilitado) {
            $item->habilitado = false;
            $text = 'deshabilitado.';
        } else {
            $item->habilitado = true;
        }
        $item->save();
        $data = [
            'success' => 'Operacion realizada correctamente. '
        ];
        return response()->json($data, 200);
    }
    public function habilitados() {
        $data = SubCategoria::get();
        return response()->json($data, 200);
    }
}

