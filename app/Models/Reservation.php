<?php

namespace App\Models;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

      protected $fillable = [
        'user_id',
        'vehicle_id',
        'slot_id',
        'qrcode_token',
        'qrcode_out',
        'expired_at',
        'start_time',
        'end_time',
        'status',
    ];

    // ✅ Otomatis ubah kolom waktu jadi instance Carbon
    protected $casts = [
        'expired_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function slot()
    {
        return $this->belongsTo(ParkingSlot::class, 'slot_id')->withDefault();
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }


    // Hitung durasi dalam jam
    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->end_time->diffInMinutes($this->start_time) / 60;
        }
        return 0;
    }

    public function parking()
{
    return $this->hasOne(Parking::class);
}

}
