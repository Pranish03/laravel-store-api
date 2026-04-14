<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    public function store(VendorRequest $request)
    {
        $validated = $request->validated();
        $email = strtolower($validated['email']);

        if (VendorProfile::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Request already exists.',
            ], 409);
        }

        VendorProfile::create([
            'shop_name'    => $validated['shop_name'],
            'email'        => $email,
            'address'      => $validated['address'],
            'phone_number' => $validated['phone_number'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your request has been sent successfully.',
        ], 201);
    }

    public function approve($id)
    {
        $vendor = VendorProfile::findOrFail($id);

        if ($vendor->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor already registered.',
            ], 400);
        }

        if ($vendor->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Vendor already processed.',
            ], 400);
        }

        return DB::transaction(function () use ($vendor) {
            $password = bin2hex(random_bytes(8));

            $user = User::create([
                'name'     => $vendor->shop_name,
                'email'    => strtolower($vendor->email),
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
