<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','slot_code', 'slot_name', 'status',
        'area_id', 'latitude', 'longitude'
    ];

    public function area()
    {
        return $this->belongsTo(ParkingArea::class, 'area_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'slot_id');
    }

    public function parkings()
    {
        return $this->hasMany(Parking::class, 'slot_id');
    }

}
