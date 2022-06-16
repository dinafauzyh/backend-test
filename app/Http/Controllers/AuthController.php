<?php

namespace App\Http\Controllers;

use Validator;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if(!Auth::attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        } else if(Auth::attempt($credentials)) {
            $token = $request->user()->createToken('access_token')->plainTextToken;
        }

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Login Successfull');
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        
        return [
            'message' => 'Logout Successfull'
        ];
    }

}
