<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index(Request $request) {
        $paginacion = 20;
        $term = $request->get('term');
        $data = Sucursal::sterm($term,'')
        ->orderBy('nombre')->paginate($paginacion);
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Sucursal::find($id);
        return response()->json($data, 200);
    }
    public function listado() {
        $data = Sucursal::get();
        // return $data;
        $arr_select = new Collection;
        $arr_select->push([
            'sucursal_id' => 0,
            'nombre' => 'ALMACEN',
        ]);
        foreach ($data as $key => $value) {
            $arr_select->push([
                'sucursal_id' => $value->sucursal_id,
                'nombre' => $value->nombre,
            ]);
        }
        return response()->json($arr_select, 200);
    }
    public function store(Request $request) {
        // no Sucursales repetidos
        $valid = Sucursal::where('nombre',$request->nombre)->first();
        if ($valid) {
            return response()->json('El nombre ya fue registrado.', 409);
        }
        $newData = new Sucursal;
        $newData->nombre = $request->nombre;
        $newData->direccion = $request->direccion;
        $newData->telefono = $request->telefono;
        $newData->habilitado = true;
        $newData->save();
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $editData = Sucursal::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        $editData->nombre = $request->nombre;
        $editData->direccion = $request->direccion;
        $editData->telefono = $request->telefono;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Sucursal::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        // $valid = ProductoSucursal::where('sucursal_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        // $valid = Stock::where('sucursal',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        // $valid = Envio::where('origen',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        // $valid = Envio::where('destino',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        $data->delete();
        return response()->json($data, 200);
    }

    public function habilitar($id) {
        $item = Sucursal::find($id);
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
        $data = Sucursal::habilitado()->get();
        return response()->json($data, 200);
    }
}
