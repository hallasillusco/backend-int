<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request) {
        $term = $request->get('term');
        $data = Proveedor::sterm($term,'')
        ->orderBy('razon_social')->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Proveedor::find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // no Proveedors repetidos
        $valid = Proveedor::where('nit',$request->nit)->first();
        if ($valid) {
            // return response()->json('El NIT ya fue registrado.', 409);
        }
        $newData = new Proveedor;
        $newData->razon_social = $request->razon_social;
        $newData->nit = $request->nit;
        $newData->telefono = $request->telefono;
        $newData->contacto = $request->contacto;
        $newData->celular = $request->celular;
        $newData->habilitado = true;
        $newData->save();
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $editData = Proveedor::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        $editData->razon_social = $request->razon_social;
        $editData->nit = $request->nit;
        $editData->telefono = $request->telefono;
        $editData->contacto = $request->contacto;
        $editData->celular = $request->celular;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Proveedor::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        // $valid = Almacen::where('proveedor_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        // $valid = Producto::where('proveedor_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        // $valid = Pedido::where('proveedor_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        $data->delete();
        return response()->json($data, 200);
    }

    public function habilitar($id) {
        $item = Proveedor::find($id);
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
        $data = Proveedor::habilitado()->get();
        return response()->json($data, 200);
    }
}
