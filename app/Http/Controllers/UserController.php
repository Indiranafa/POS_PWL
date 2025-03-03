<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        
        $data = [
            'level_id' => 2,
            'username' => 'manager_empat',
            'nama' => 'Manager 4',
            'password' => Hash::make('12345')
        ];
        // UserModel::where('username', 'manager_dua')->delete();
        // UserModel::where('username', 'manager_tiga')->delete();
        UserModel::create($data);
    
        $user = UserModel::all();
        return view('user.index', ['data' => $user]);
    }
    
}
