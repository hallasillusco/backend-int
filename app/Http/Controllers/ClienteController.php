<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proforma;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request) {
        $paginacion = 20;
        $term = $request->get('term');
        $data = Cliente::sterm($term,'')
        ->orderBy('razon_social')
        ->with(['tipo_cliente'])
        ->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Cliente::with(['tipo_cliente'])->find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // no Clientes repetidos
        $valid = Cliente::where('razon_social',$request->razon_social)->first();
        if ($valid) {
            return response()->json('La razon social ya fue registrada.', 409);
        }
        $newData = new Cliente();
        $newData->razon_social = $request->razon_social;
        $newData->nombre_completo = $request->nombre_completo;
        $newData->nit = $request->nit;
        $newData->celular = $request->celular;
        $newData->direccion = $request->direccion;
        $newData->email = $request->email;
        $newData->habilitado = true;
        $newData->tipo_cliente_id = $request->tipo_cliente_id;
        $newData->save();
        $data = [
            'cliente' => $newData,
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $valid = Cliente::where('cliente_id','!=',$id)
        ->where('razon_social',$request->razon_social)
        ->first();
        if ($valid) {
            return response()->json('La razon social ya fue registrada.', 409);
        }
        $editData = Cliente::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        $editData->razon_social = $request->razon_social;
        $editData->nombre_completo = $request->nombre_completo;
        $editData->nit = $request->nit;
        $editData->celular = $request->celular;
        $editData->direccion = $request->direccion;
        $editData->email = $request->email;
        $editData->tipo_cliente_id = $request->tipo_cliente_id;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Cliente::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        $valid = Proforma::where('cliente_id',$id)->first();
        if ($valid) {
            return response()->json('No puede ser eliminado', 409);
        }
        // $valid = Venta::where('cliente_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado', 409);
        // }
        $data->delete();
        return response()->json($data, 200);
    }
    public function habilitar($id) {
        $item = Cliente::find($id);
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
        $data = Cliente::habilitado()->get();
        return response()->json($data, 200);
    }
    public function buscar(Request $request) {
        $email = $request->get('email');
        $nit = $request->get('nit');
        $data = Cliente::where('email',$email)
        ->where('nit',$nit)->first();
        if (!$data) {
            return response()->json('No encontrado', 409);
        }
        return response()->json($data, 200);
    }
}
