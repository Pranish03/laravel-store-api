<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'address'      => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ];
    }
}
