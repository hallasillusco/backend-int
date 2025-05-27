<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index(Request $request) {
        $data = Marca::latest()->get();
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        $name = null;
        $dir_folder = 'images/productos';
        if($request->hasFile('file')){
            if (!is_dir(public_path().'/'.$dir_folder)) {
                $crearDir = mkdir(public_path().'/'.$dir_folder, 0777, true);
            }
            $file = $request->file('file');
            //concatena la hora y el nombre del archivo
            $name = $file->getClientOriginalName();
            $file->move(public_path().'/'.$dir_folder, $name);
            $ruta = public_path().'/'.$dir_folder.'/'. $name;
        }
        $newItem = new Marca;
        if ($name) {
            $newItem->img_url = $dir_folder.'/'.$name;
        }
        $newItem->nombre = $request->nombre;
        $newItem->habilitado = true;
        $newItem->save();
        $data = [
            'success' => 'Operacion realizada correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function update(Request $request, $id) {
        $name = null;
        $dir_folder = 'images/productos';
        if($request->hasFile('file')){
            if (!is_dir(public_path().'/'.$dir_folder)) {
                $crearDir = mkdir(public_path().'/'.$dir_folder, 0777, true);
            }
            $file = $request->file('file');
            //concatena la hora y el nombre del archivo
            $name = $file->getClientOriginalName();
            $file->move(public_path().'/'.$dir_folder, $name);
            $ruta = public_path().'/'.$dir_folder.'/'. $name;
        }
        $newItem = Marca::find($id);
        if (!$newItem) {
            return response()->json('No encontrado.', 409);
        }
        if ($name) {
            $newItem->img_url = $dir_folder.'/'.$name;
        }
        $newItem->nombre = $request->nombre;
        $newItem->save();
        $data = [
            'success' => 'Registro actualizado correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Marca::find($id);
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $newItem = Marca::find($id);
        if (!$newItem) {
            return response()->json('No encontrado.', 409);
        }
        $valid = Producto::where('marca_id',$id)->first();
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
        $item = Marca::find($id);
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
        $data = Marca::get();
        return response()->json($data, 200);
    }
}

