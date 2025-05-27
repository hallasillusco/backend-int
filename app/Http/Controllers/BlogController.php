<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\TipoBlog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function tipos() {
        $data = TipoBlog::get();
        return response()->json($data, 200);
    }
    public function index(Request $request) {
        $term = $request->get('term');
        $data = Blog::with(['tipo'])->get();
        return response()->json($data, 200);
    }
    public function show($id) {
        $data = Blog::with(['tipo'])->find($id);
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        // no Blogs repetidos
        $valid = Blog::where('nombre',$request->nombre)->first();
        if ($valid) {
            return response()->json('El nombre ya fue registrado.', 409);
        }
        $name = null;
        $dir_folder = 'images/blogs';
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
        $newData = new Blog;
        $newData->nombre = $request->nombre;
        if ($name) {
            $newData->img_url = $dir_folder.'/'.$name;
        }
        $newData->descripcion = $request->descripcion;
        $newData->tipo_blog_id = $request->tipo_blog_id;
        $newData->habilitado = true;
        $newData->save();
        $data = [
            'success' => 'Se ha realizado un nuevo registro.'
        ];
        return response()->json($data, 201);
    }
    public function update(Request $request, $id) {
        $editData = Blog::find($id);
        if (!$editData) {
            return response()->json('Ha ocurrido un error.', 409);
        }
        $name = null;
        $dir_folder = 'images/blogs';
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
        if ($name) {
            $editData->img_url = $dir_folder.'/'.$name;
        }
        $editData->nombre = $request->nombre;
        $editData->descripcion = $request->descripcion;
        $editData->tipo_blog_id = $request->tipo_blog_id;
        $editData->save();

        $data = [
            'success' => 'Se ha actualizado el registro.'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id) {
        $data = Blog::find($id);
        if (!$data) {
            return response()->json($data, 409);
        }
        $data->delete();
        return response()->json($data, 200);
    }

    public function habilitar($id) {
        $item = Blog::find($id);
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
        $data = Blog::habilitado()->get();
        return response()->json($data, 200);
    }
}
