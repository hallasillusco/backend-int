<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request) {
        $term = $request->get('term');
        $data = Categoria::sterm($term,'')
        ->orderBy('nombre')
        ->with(['tipo'])
        ->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Categoria::with(['tipo'])->find($id);
        return response()->json($data, 200);
    }
    public function showByTipo($id) {
        $data = Categoria::where('tipo_id',$id)->get();
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // no Categorias repetidos
        $valid = Categoria::where('nombre',$request->nombre)->first();
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
        $newData = new Categoria;
        $newData->nombre = $request->nombre;
        if ($name) {
            $newData->img_url = $dir_folder.'/'.$name;
        }
        $newData->tipo_id = $request->tipo_id;
        $newData->habilitado = true;
        $newData->save();
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $editData = Categoria::find($id);
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
        $editData->tipo_id = $request->tipo_id;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Categoria::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        $valid = Producto::where('categoria_id',$id)->first();
        if ($valid) {
            return response()->json('No puede ser eliminado', 409);
        }
        $data->delete();
        return response()->json($data, 200);
    }

    public function habilitar($id) {
        $item = Categoria::find($id);
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
        $data = Categoria::habilitado()->get();
        return response()->json($data, 200);
    }
}
