<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\TipoCliente;
use Illuminate\Http\Request;

class TipoClienteController extends Controller
{
    
    public function index(Request $request) {
        $data = TipoCliente::latest()->get();
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        $newItem = new TipoCliente;
        $newItem->nombre = $request->nombre;
        $newItem->descuento = $request->descuento;
        $newItem->habilitado = true;
        $newItem->save();
        $data = [
            'success' => 'Operacion realizada correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function update(Request $request, $id) {
        $newItem = TipoCliente::find($id);
        if (!$newItem) {
            return response()->json('No encontrado.', 409);
        }
        $newItem->nombre = $request->nombre;
        $newItem->descuento = $request->descuento;
        $newItem->save();
        $data = [
            'success' => 'Registro actualizado correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = TipoCliente::find($id);
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $newItem = TipoCliente::find($id);
        if (!$newItem) {
            return response()->json('No encontrado.', 409);
        }
        $valid = Cliente::where('tipo_cliente_id',$id)->first();
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
        $item = TipoCliente::find($id);
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
        $data = TipoCliente::where('habilitado',true)->get();
        return response()->json($data, 200);
    }
}
