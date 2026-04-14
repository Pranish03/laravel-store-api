<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;

class VendorController extends Controller
{
    public function requestVendor(VendorRequest $request)
    {


        return response()->json([
            'success' => true,
            'message' => 'Your request has been sent successfully.',
            'data'    => null
        ], 201);
    }
}
