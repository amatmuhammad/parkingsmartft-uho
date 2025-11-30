<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredReservations extends Command
{
    protected $signature = 'reservations:check-expired';
    protected $description = 'Check and expire reservations that passed expired_at';

    public function handle()
    {
        // $expiredReservations = Reservation::where('status', 'booked')
        //     ->where('expired_at', '<=', Carbon::now())
        //     ->get();
        $expiredReservations = Reservation::where('status', 'booked')
            ->whereNull('start_time')
            ->where('expired_at', '<=', Carbon::now())
            ->get();


        $count = 0;
        
        foreach ($expiredReservations as $reservation) {

            $reservation->update([
                'status'     => 'expired',
                'start_time' => $reservation->start_time ?? Carbon::parse($reservation->created_at),
                'end_time'   => Carbon::now(),    // Waktu expired
            ]);

            if ($reservation->slot) {
                $reservation->slot->update(['status' => 'available']);
            }
            
            $count++;

            Log::info("🕐 Reservasi expired otomatis", [
                'reservation_id' => $reservation->id,
                'user_id'        => $reservation->user_id,
                'expired_at'     => $reservation->expired_at,
                'end_time_saved' => Carbon::now(),
            ]);
        }

        $this->info("{$count} reservasi telah di-expire otomatis.");
        Log::info("🕐 Scheduler: {$count} reservasi expired");

        return 0;
    }
}
