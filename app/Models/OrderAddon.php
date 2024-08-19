<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'addon_id',
        'product_id',
        'qty',
        'amount',
    ];
}
