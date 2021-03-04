<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected function login(Request $request){
        $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return response()->json([
            "result"=> true,
            "message" => "Login Success"
        ]);
    }

return response()->json([
    "result"=> false,
    "message" => "Wrong Credentials",
    "errors" => ['email' => 'The provided credentials do not match our records.'],
]);

    }
}
