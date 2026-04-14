<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'shop_name', 'email', 'address', 'phone_number', 'status'])]

class VendorProfile extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
