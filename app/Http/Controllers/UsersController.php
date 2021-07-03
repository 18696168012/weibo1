<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            'except'=>['show','create','store','index']
        ]);
        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
    public function index(){
        $users=User::paginate(10);
        return view('users.index',compact('users'));
    }
    public function create(){
        return view('users.create');
    }
    public function show(User $user){
        return view('users.show',compact('user'));
    }
    public function store(Request $request){
        $this->validate($request,[
            'name'=>'required|unique:users|max:50',
            'email'=>'required|email|max:255|unique:users',
            'password'=>'required|unique:users|confirmed|min:6'
        ]);
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        session()->flash('success','注册成功了');
        Auth::login($user);
        return redirect()->route('users.show',[$user]);
    }
    public function edit(User $user){
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }
    public function update(User $user,Request $request){
        $this->authorize('update',$user);
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|mix:6'
        ]);
        $data=[];
        if($request->password){
            $data['password']=$request->password;
        }
        $data['name']=$request->name;
        $re=$user->update($data);
        if($re){
            //成功
            session()->flash('success','更新成功');
            return redirect()->route('users.show',[$user]);
        }else{
            //失败
            session()->flash('danger','更新失败');
            return redirect()->back()->withInput();
        }
    }
    public function destroy(User $user){
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','删除成功');
        //return redirect()->route('users.index');
        return back();
    }
}
