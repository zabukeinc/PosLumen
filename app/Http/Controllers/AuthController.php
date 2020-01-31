<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {


    public function register(Request $request){
        $validationRules = [
            'username' => 'required|unique:users|max:255',
            'password' => 'required|min:6',
            'level' => 'required|in:admin,pegawai',
            'nama_pegawai' => 'required|min:6',
            'alamat_pegawai' => 'required:min:6'
        ];

        $input = $request->all();

        $validator = \Validator::make($input, $validationRules);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }


        $user = new Users;
        $user->username = $request->input('username');
        $plainPassword = $request->input('password');
        $user->password = app('hash')->make($plainPassword);
        $user->save();

        $account = new Account;
        $account->nama_pegawai = $request->input('nama_pegawai');
        $account->alamat_pegawai = $request->input('alamat_pegawai');
        $account->id_user = $user->id_users;
        $account->save();

        return response()->json(array('user' => $user, 'detail_user' => $account), 200);
    }


    public function login(Request $request){
        $input = $request->all();

        $validationRules = [
            'username' => 'required|min:6',
            'password' => 'required|min:6'
        ];

        $validator = \Validator::make($input, $validationRules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        // proses login
        $credentials = $request->only(['username', 'password']);

        if(!$token = Auth::attempt($credentials)){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 3600
        ], 200);

    }
}