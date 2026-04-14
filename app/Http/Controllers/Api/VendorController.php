<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\VendorProfile;

class VendorController extends Controller
{
    public function store(VendorRequest $request)
    {
        $validated = $request->validated();

        VendorProfile::create([
            'shop_name'    => $validated['shop_name'],
            'email'        => strtolower($validated['email']),
            'address'      => $validated['address'],
            'phone_number' => $validated['phone_number'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your request has been sent successfully.',
        ], 201);
    }
}
