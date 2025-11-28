<?php
// app/Services/BookingService.php

namespace App\Services;

use App\Models\ParkingSlot;
use App\Models\Payment;
use App\Models\Pricing;
use App\Models\Slot;

class BookingService
{
    public static function calculateAmount($vehicleType, $startTime, $endTime = null)
    {
        $pricing = Payment::getPrice($vehicleType);
        $endTime = $endTime ?? now();
        
        $totalHours = ceil($startTime->diffInMinutes($endTime) / 60);
        
        if ($totalHours <= 1) {
            $amount = $pricing->first_hour_price;
        } else {
            $amount = $pricing->first_hour_price + (($totalHours - 1) * $pricing->next_hour_price);
        }
        
        if ($pricing->daily_max && $amount > $pricing->daily_max) {
            $amount = $pricing->daily_max;
        }
        
        return $amount;
    }
    
    public static function findAvailableSlot($vehicleType, $polygonId = null)
    {
        $query = ParkingSlot::where('vehicle_type', $vehicleType)
                    ->where('status', 'available');
                    
        if ($polygonId) {
            $query->where('polygon_id', $polygonId);
        }
        
        return $query->first();
    }
}