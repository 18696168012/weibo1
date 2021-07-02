<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class SessionsCOntroller extends Controller
{
    public function create(){
        return view('sessions.create');
    }
    public function store(Request $request){
        $param=$this->validate($request,[
            'email'=>'required|email|min:6',
            'password'=>'required',
        ]);
        if(Auth::attempt($param)){
            session()->flash('success','登录成功');
            return redirect()->route('users.show',[Auth::user()]);
            echo 'ok';
        }else{
            session()->flash('danger','对不起,登录失败');
            return redirect()->back()->withInput();
        }
    }
    public function destroy(){
        Auth::logout();
        session()->flash('success','您已经成功退出');
        return redirect()->route('login');
    }
}
