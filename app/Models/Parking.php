<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'vehicle_id',
        'slot_id',
        'start_time',
        'end_time',
        'total_fee',
        'status',
    ];

    protected $casts  = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================
    //      RELATIONSHIP
    // ============================

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function slot()
    {
        return $this->belongsTo(ParkingSlot::class);
    }
}
