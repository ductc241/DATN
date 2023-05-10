<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return true;
        } else {
            return back();
        }
    }

    public function Login(LoginRequest $request)
    {
        if (Auth::attempt($request->only(['email','password']))) {
            $user = Auth::user();
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            Session::flash('successLoign', '{ "correct" : "đăng nhập thành công!" }');
            return Response()->json([
               "token"=> $tokenResult,
                json_decode(Session::get('successLoign')),
                Auth::user()
            ]);
        } else {
            Session::flash('failureLoign', '{ "error" : "tài khoản hoặc mật khẩu sai!" }');
            return json_decode(Session::get('failureLoign'));
        }
    }

    public function Logout()
    {
        Auth::logout();
        return back();
    }
}
