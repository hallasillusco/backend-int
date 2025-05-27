<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Models\TipoCategoria;

class TipoCategoriaController extends Controller
{
    public function index(Request $request) {
        $term = $request->get('term');
        $data = TipoCategoria::get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = TipoCategoria::find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // no Categorias repetidos
        $valid = TipoCategoria::where('nombre',$request->nombre)->first();
        if ($valid) {
            return response()->json('El nombre ya fue registrado.', 409);
        }
        $name = null;
        $dir_folder = 'images/categorias';
        if($request->hasFile('file')){
            // if (!is_dir(public_path().'/'.$dir_folder)) {
            //     $crearDir = mkdir(public_path().'/'.$dir_folder, 0777, true);
            // }
            // $file = $request->file('file');
            // //concatena la hora y el nombre del archivo
            // $name = $file->getClientOriginalName();
            // $file->move(public_path().'/'.$dir_folder, $name);
            // $ruta = public_path().'/'.$dir_folder.'/'. $name;
        }
        $newData = new TipoCategoria;
        $newData->nombre = $request->nombre;
        if ($name) {
            $newData->img_url = $dir_folder.'/'.$name;
        }
        $newData->habilitado = true;
        $newData->menu = true;
        $newData->save();
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $editData = TipoCategoria::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        $name = null;
        $dir_folder = 'images/categorias';
        if($request->hasFile('file')){
            // if (!is_dir(public_path().'/'.$dir_folder)) {
            //     $crearDir = mkdir(public_path().'/'.$dir_folder, 0777, true);
            // }
            // $file = $request->file('file');
            // //concatena la hora y el nombre del archivo
            // $name = $file->getClientOriginalName();
            // $file->move(public_path().'/'.$dir_folder, $name);
            // $ruta = public_path().'/'.$dir_folder.'/'. $name;
        }
        if ($name) {
            $editData->img_url = $dir_folder.'/'.$name;
        }
        $editData->nombre = $request->nombre;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = TipoCategoria::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        $valid = Categoria::where('tipo_id',$id)->first();
        if ($valid) {
            return response()->json('No puede ser eliminado', 409);
        }
        $data->delete();
        return response()->json($data, 200);
    }

    public function habilitar($id) {
        $item = TipoCategoria::find($id);
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
        $data = TipoCategoria::habilitado()->get();
        return response()->json($data, 200);
    }
}
