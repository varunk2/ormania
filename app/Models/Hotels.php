<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Hotels extends Model
{
    protected $table = 'hotels';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'city',
        'country',
        'price_per_night',
        'user_id'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    protected function location(): Attribute {
        return Attribute::make (
            get: fn (mixed $value, array $attributes) => $attributes['city'] . ', ' . $attributes['country']
        );
    }

    protected function pricePerNight(): Attribute {
        return Attribute::make (
            get: fn (string $value) => $value . ' / night'
        );
    }
}
