<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingArea extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'gate','type', 'polygon_coordinates'];

    protected $casts = [
        'polygon_coordinates' => 'array'
    ];

    public function slots()
    {
        return $this->hasMany(ParkingSlot::class, 'area_id');
    }
}
