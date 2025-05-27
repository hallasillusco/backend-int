<?php

namespace App\Http\Controllers;

use DB;
use Validator;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        $term = $request->input('term');
        $rol = $request->input('rol');

        $query = User::sterm($term,'')
        ->srol($rol,'')
        ->with(['rol'])
        ->latest()
        ->get();
        return response()->json($query, 200);
    }
    public function show($id) {
        $data = User::with('rol')->find($id);
        return response()->json($data, 200);
        
        // $user = User::find($id);
        // $data = User::find($id);
        // $roles = Role::get();
        // foreach ($roles as $rol) {
        //     $rol->selected = $user->hasRole($rol->name);
        // }
        // $data->roles = $roles;
        // return response()->json($data, 200);
    }
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            // 'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['Algunos campos son requeridos.'], 409);
        }
        
        $valUserName = User::where('username',$request->username)->first();
        if ($valUserName) {
            return response()->json('El nombre de usuario ya esta en uso.', 409);
        }

        // $valEmail = User::where('email',$request->email)->first();
        // if ($valEmail) {
        //     return response()->json('El email ya esta en uso.', 409);
        // }
        $fullname = null;
        if ($request->nombres) {
            if ($request->apellidos) {
                $fullname = $request->nombres.' '.$request->apellidos;
            } else {
                $fullname = $request->nombres;
            }
        } else {
            if ($request->apellidos) {
                $fullname = $request->apellidos;
            }
        }

        try {
            DB::beginTransaction();
            $newItem = new User;
            $newItem->nombres = $request->nombres;
            $newItem->apellidos = $request->apellidos;
            $newItem->nombre_completo = $fullname;
            $newItem->ci = $request->ci;
            $newItem->celular = $request->celular;
            $newItem->username = $request->username;
            $newItem->password = bcrypt($request->password);
            $newItem->email = $request->email;
            $newItem->habilitado = true;
            $newItem->rol_id = $request->rol_id;
            $newItem->save();

            DB::commit();
            return response()->json(['success' => 'Operación realizada exitosamente.'], 201);
        } catch (\Exception $th) {
            DB::rollback();
            //throw $th;
            return response()->json($th, 409);
        }
    }
    public function update(Request $request, $id) {
        $valUserName = User::where('username',$request->username)
        ->where('id','!=',$id)->first();
        if ($valUserName) {
            return response()->json('El nombre de usuario ya esta en uso.', 409);
        }

        // $valEmail = User::where('email',$request->email)
        // ->where('id','!=',$id)->first();
        // if ($valEmail) {
        //     return response()->json('El email ya esta en uso.', 409);
        // }
        $editItem = User::find($id);
        if (!$editItem) {
            return response()->json('No se encuentra el usuario en el sistema, COD-ID: '.$id, 409);
        }
        $fullname = null;
        if ($request->nombres) {
            if ($request->apellidos) {
                $fullname = $request->nombres.' '.$request->apellidos;
            } else {
                $fullname = $request->nombres;
            }
        } else {
            if ($request->apellidos) {
                $fullname = $request->apellidos;
            }
        }
        $editItem->nombres = $request->nombres;
        $editItem->apellidos = $request->apellidos;
        $editItem->nombre_completo = $fullname;
        $editItem->ci = $request->ci;
        $editItem->celular = $request->celular;
        $editItem->save();
        
        $editItem->username = $request->username;
        $editItem->email = $request->email;
        if ($request->password) {
            $editItem->password = bcrypt($request->password);
        }
        $editItem->rol_id = $request->rol_id;
        $editItem->save();
        
        return response()->json(['success' => 'Operación realizada exitosamente.'], 200);
    }

    public function destroy($id) {
        $item = User::find($id);
        if(!$item) {
            return response()->json($item, 409);
        }
        // $valid = Ingreso::where('user_id',$id)->first();
        // if ($valid) {
        //     return response()->json('No puede ser eliminado.', 409);
        // }
        $item->delete();

        return response()->json($item, 200);
    }
    public function habilitar($id) {
        $item = User::find($id);
        $text = 'habilitado.';
        if ($item->habilitado) {
            $item->habilitado = false;
            $text = 'deshabilitado.';
        } else {
            $item->habilitado = true;
        }
        $item->save();
        return response()->json(['success' => 'Operación realizada correctamente. '.$text], 200);
    }
    public function roles() {
        return response()->json(Role::get(), 200);
    }
}
