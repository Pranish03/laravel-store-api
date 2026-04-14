<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VendorRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => strtolower($validated['email']),
            'password' => Hash::make($validated['password'])
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registered successfully.',
            'token'   => $token,
            'data'    => new UserResource($user)
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (!Auth::attempt($validated)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'token'   => $token,
            'data'    => new UserResource($user)
        ], 200);
    }

    public function logout(Request $request)
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => new UserResource($request->user())
        ]);
    }

    public function registerVendor(VendorRequest $request)
    {
        $validated = $request->validated();
        $password = str()->random(12);

        return DB::transaction(function () use ($validated, $password) {
            $email = strtolower($validated['email']);
            $vendor = VendorProfile::where('email', $email)->first();

            if (! $vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found.',
                ], 404);
            }

            if ($vendor->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor already processed.',
                ], 400);
            }

            if ($vendor->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor already registered.',
                ], 400);
            }

            $user = User::create([
                'name'     => $validated['shop_name'],
                'email'    => $email,
                'password' => Hash::make($password),
                'role'     => 'vendor'
            ]);

            $vendor->update([
                'user_id' => $user->id,
                'status'  => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vendor registered successfully.',
                'data'    => new UserResource($user)
            ], 201);
        });
    }
}
