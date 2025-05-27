<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index(Request $request) {
        $data = Config::first();
        return response()->json($data, 200);
    }
    public function store(Request $request) {
        $name = null;
        $dir_folder = 'images/config';
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
        $newItem = Config::first();
        if ($name) {
            $newItem->img_url = $dir_folder.'/'.$name;
        }
        $newItem->razon_social = $request->razon_social;
        $newItem->direccion = $request->direccion;
        $newItem->telefono = $request->telefono;
        $newItem->pago_bancario = $request->pago_bancario?$request->pago_bancario:false;
        $newItem->pago_qr = $request->pago_qr?$request->pago_qr:false;
        $newItem->save();
        $data = [
            'success' => 'Operacion realizada correctamente.'
        ];
        return response()->json($data, 200);
    }
    public function update(Request $request, $id) {
    }
    public function show($id) {
    }
    public function destroy($id) {
    }
}
