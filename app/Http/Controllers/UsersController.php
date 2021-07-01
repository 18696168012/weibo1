<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function create(){
        return view('users.create');
    }
    public function show(User $user){
        return view('users.show',compact('user'));
    }
    public function store(Request $request){
        $this->validate($request,[
            'name'=>'required|unique:users|max:50',
            'email'=>'required|email|max:255',
            'password'=>'required|unique:users|confirmed|mix:6'
        ]);
    }
}
