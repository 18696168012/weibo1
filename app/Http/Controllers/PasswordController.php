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
}
