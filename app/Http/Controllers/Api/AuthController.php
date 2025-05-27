<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\User;
use App\Models\Acceso;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        $val = '';
        if ($request->email) {
            $val = $request->email;
        }
        if ($request->username) {
            $val = $request->username;
        }
        // if(Auth::attempt(['email' => $val, 'password' => $request->password]) || Auth::attempt(['username' => $val, 'password' => $request->password])){
        if(Auth::attempt(['username' => $val, 'password' => $request->password])){
            $user = Auth::user();
            $userObj = User::where('id',$user->id)->with(['rol'])->first();

            if ($userObj->habilitado == true) {
                $token = $user->createToken('LaraPassport');
                $success['token'] =  $token->accessToken;
                $success['user'] =  $userObj;
                
                $new = new Acceso;
                $new->ingreso = date('Y-m-d H:i:s');
                // $new->salida = date('Y-m-d H:i:s');
                $new->user_id = Auth::user()->id;
                $new->save();
                
                return response()->json(['status' => 'success', 'data' => $success]);
            } else {
                return response()->json('Usuario no habilitado', 409);
            }
        } else {
            return response()->json('Usuario no registrado', 409);
        }
    }
    public function updatePassword(Request $request) {
        $validator = Validator::make($request->all(),[
            'current_password' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'algunos campos son requeridos'
            ], 400);
        }

        $user = Auth::user();
        if(Hash::check($request->get('current_password'), $user->password)){
            $userObj = User::with(['rol'])->find($user->id);

            if ($userObj->habilitado == true) {
                $updatePassword = User::find($user->id);

                $updatePassword->password = bcrypt($request->password);
                $updatePassword->save();
                $data = [
                    'success' => 'Contraseña actualizada.'
                ];

                return response()->json($data, 200);
            } else {
                return response()->json('Usuario no habilitado', 409);
            }
        } else {
            return response()->json('Usuario no registrado', 409);
        }
    }
    public function logout() {
        $user = Auth::user()->token();
        $user->revoke();
        $data = [
            'success' => 'Sesión finalizada'
        ];
        return response()->json($data, 200);
    }
}
