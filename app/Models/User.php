<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Vehicle;
use App\Models\Reservation;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use Notifiable;

     protected $fillable = ['name', 'email', 'password', 'phone', 'role'];

    public function vehicles()
    {
       return $this->hasMany(Vehicle::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
