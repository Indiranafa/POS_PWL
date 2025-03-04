<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $user = UserModel::all();
        return view('user.index', ['data' => $user]);
    }
    
    public function tambah(){
        return view('user.user_tambah');
    }

    public function tambah_simpan(Request $request){
        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => Hash::make($request->password),
            'level_id' => $request->level_id,
        ]);
        return redirect('/user');
    }

    public function ubah($user_id){
        $user = UserModel::find($user_id);
        return view('user.user_ubah', ['data' => $user]);
    }

    public function ubah_simpan($user_id, Request $request){
        $user = UserModel::find($user_id);

        $user->username = $request->username;
        $user->nama = $request->nama;
        $user->password = Hash::make($request->password);
        $user->level_id = $request->level_id;
        $user->save();
        return redirect('/user');
    }

    public function hapus($user_id){
        $user = UserModel::find($user_id);
        $user->delete();
        return redirect('/user');
    }
}
