<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * dev login
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = Auth::user();

        // create token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user
        ], 200);
    }

    /**
     * dev logout
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
