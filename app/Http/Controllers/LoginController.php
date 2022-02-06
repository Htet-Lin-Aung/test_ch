<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {    
        
        if (!auth()->attempt($request->only('email', 'password')))
        {
            return response()
                ->json(['message' => 'Invalid Credentials'], 401);
        }

        $user = User::where('email', $request->email)->first();
        
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('User-Token')->plainTextToken
        ]);
    }
}
