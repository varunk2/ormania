<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelUser extends Model
{
    protected $table = "hotel_user";

    protected $fillable = [
        "user_id",
        "hotel_id"
    ];
}
