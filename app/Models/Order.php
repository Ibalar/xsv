<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['products', 'name', 'phone', 'agree'];

    protected $casts = [
        'products' => 'array',
        'agree' => 'boolean',
    ];
}
