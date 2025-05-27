<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request) {
        $term = $request->get('term');
        $data = Video::get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Video::find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // no Videos repetidos
        $valid = Video::where('nombre',$request->nombre)->first();
        if ($valid) {
            return response()->json('El nombre ya fue registrado.', 409);
        }
        $newData = new Video;
        $newData->nombre = $request->nombre;
        $newData->link = $request->link;
        $newData->habilitado = true;
        $newData->save();
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $editData = Video::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        $editData->nombre = $request->nombre;
        $editData->link = $request->link;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Video::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        $data->delete();
        return response()->json($data, 200);
    }

    public function habilitar($id) {
        $item = Video::find($id);
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
        $data = Video::habilitado()->get();
        return response()->json($data, 200);
    }
}
