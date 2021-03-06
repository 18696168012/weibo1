<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:3,10',[
            'only'=>['showLinkRequestForm']
        ]);
    }

    public function showLinkRequestForm(){
        //dd(new Carbon());
        return view('passwords.email');
    }
    public function sendResetLinkEmail(Request $request){
        $this->validate($request,['email'=>'required|email']);
        $email=$request->email;
        //查找用户
        $user=User::where('email',$email)->first();
        if(is_null($user)){
            session()->flash('danger','未找到用户信息');
            return redirect()->back()->withInput();
        }
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        DB::table('password_resets')->updateOrInsert(['email'=>$email],[
            'email'=>$email,
            'token'=>Hash::make($token),
            'created_at'=>new Carbon(),
        ]);
        Mail::send('emails.reset_link', compact('token'), function ($message) use ($email) {
            $message->to($email)->subject("忘记密码");
        });

        session()->flash('success', '重置邮件发送成功，请查收');
        return redirect()->back();
    }
    public function showResetForm(Request $request){
        $token=$request->route()->parameter('token');
        return view('passwords.reset',compact('token'));
    }
    public function reset(Request $request){
        $this->validate($request,[
            'email'=>'required|email',
            'token'=>'required',
            'password'=>'required|confirmed|min:6'
        ]);
        $email=$request->email;
        $token=$request->token;
        $password=$request->password;
        $expires=60*10;
        //查找用户
        $user=User::where('email',$email)->first();
        if(is_null($user)){
            session()->flash('danger','未找到邮箱');
            return redirect()->back()->withInput();
        }
        //查找重置的记录
        $record=(array)DB::table('password_resets')->where('email',$email)->first();
        if($record){
            // 5.1. 检查是否过期
            if (Carbon::parse($record['created_at'])->addSeconds($expires)->isPast()) {
                session()->flash('danger', '链接已过期，请重新尝试');
                return redirect()->back();
            }

            // 5.2. 检查是否正确
            if ( ! Hash::check($token, $record['token'])) {
                session()->flash('danger', '令牌错误');
                return redirect()->back();
            }

            // 5.3. 一切正常，更新用户密码
            $user->update(['password' => bcrypt($request->password)]);

            // 5.4. 提示用户更新成功
            session()->flash('success', '密码重置成功，请使用新密码登录');
            return redirect()->route('login');
        }else{
            session()->flash('danger','未找到重置记录');
            return redirect()->back()->withInput();
        }
    }
}
