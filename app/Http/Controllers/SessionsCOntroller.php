<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class SessionsCOntroller extends Controller
{
    public function __construct()
    {
        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }

    public function create(){
        return view('sessions.create');
    }
    public function store(Request $request){
        $param=$this->validate($request,[
            'email'=>'required|email|min:6',
            'password'=>'required',
        ]);
        if(Auth::attempt($param,$request->has('remember'))){
            if(Auth::user()->activated){
                session()->flash('success','登录成功');
                $fallback=route('users.show',[Auth::user()]);
                //dd($fallback);
                return redirect()->intended($fallback);
            }else{
                Auth::logout();
                session()->flash('warning','未激活');
                //dd($fallback);
                return redirect()->route('login');
            }

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
