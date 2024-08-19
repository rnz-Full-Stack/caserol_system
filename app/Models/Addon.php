<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;




    public static function addonDetails($addon_code) {
        $addonDetails = Addon::where('addon_code', $addon_code)->first()->toArray();


        return $addonDetails;
    }
}
