<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password'])
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registered successfully',
            'token' => $token,
            'user' => new UserResource($user)
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (! Auth::attempt($validated)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Loggedin successfully',
            'token' => $token,
            'user' => new UserResource($user)
        ], 200);
    }
}
