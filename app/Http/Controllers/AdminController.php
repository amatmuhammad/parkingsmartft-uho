<?php

namespace App\Http\Controllers;

use App\Models\ParkingSlot;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function index(){
            return view('administrator.dashboard', [
                'booked'    => Reservation::where('status', 'booked')->count(),
                'active'    => Reservation::where('status', 'active')->count(),
                'completed' => Reservation::where('status', 'completed')->count(),
            ]);
    }

    public function indexjson()
    {
        $today = now()->startOfDay();

        return response()->json([
            'active'    => Reservation::where('status','active')
                            ->whereDate('created_at', $today)
                            ->count(),

            'booked'    => Reservation::where('status','booked')
                            ->whereDate('created_at', $today)
                            ->count(),

            'completed' => Reservation::where('status','completed')
                            ->whereDate('created_at', $today)
                            ->count(),

            'slots'     => ParkingSlot::where('status','available')
                            ->count(),
        ]);
    }


    public function maps(){
        return view('administrator.maps-slot');
    }

    public function manage(){
        return view('administrator.manage');
    }   

    public function booking(){
        $users = User::all();
        // $vehicles = Vehicle::all();
        $slots = ParkingSlot::all();
        $reservations = Reservation::orderBy('id','desc')->get();
        $vehicles = Vehicle::all();


        return view('administrator.booking', compact('users','vehicles','slots','reservations'));

    }

    


    public function json()
    {
        $reservations = Reservation::with('user','vehicle','slot')->get();
        return response()->json($reservations);
    }


    public function parked (){
         $reservations = Reservation::with(['user', 'vehicle', 'slot'])
            ->where('status', 'active') // hanya yang sedang parkir
            ->latest()
            ->get();

        return view('administrator.parking', compact('reservations'));
    }

    public function out(){
        return view('administrator.out');
    }

    public function jsonout()
    {
        $reservations = Reservation::with(['user','vehicle','slot'])
                        ->where('status','completed')
                        ->orderBy('id', 'asc')
                        ->get();

        return response()->json($reservations);
    }

    public function users(){
        return view('manage.users');
    }

    public function getData()
    {
        $users = User::with('vehicles')->get()->map(function ($user) {

            $vehicle = $user->vehicles->first(); // Ambil kendaraan pertama

            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'role'         => ucfirst($user->role),
                'phone'        => $user->phone ?? '-',
                'vehicle_type' => $vehicle->vehicle_type ?? '-',
                'plate_number' => $vehicle->plate_number ?? '-',
                'created_at'   => $user->created_at->format('Y-m-d'),
            ];
        });


        return response()->json(['data' => $users]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $user->delete(); // vehicles ikut terhapus karena cascade

        return response()->json(['message' => 'User dan kendaraannya berhasil dihapus']);
    }


    public function price(){
        return view('manage.price');
    }

}
