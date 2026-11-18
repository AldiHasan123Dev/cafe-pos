<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();

            return response()->json([
                'message' => 'Akun tidak aktif.',
            ], 403);
        }

        $user->load('role.permissions');

        $token = $user->createToken('cafe-pos-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',

            'data' => [
                'token' => $token,

                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->name,

                    'permissions' => $user->role
                        ->permissions
                        ->pluck('name'),
                ],
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('role.permissions');

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name,
                'permissions' => $user->role
                    ->permissions
                    ->pluck('name'),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}