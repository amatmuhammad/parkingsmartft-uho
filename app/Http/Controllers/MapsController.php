<?php

namespace App\Http\Controllers;

use App\Models\ParkingArea;
use Illuminate\Http\Request;

class MapsController extends Controller
{
    //

    public function index(){

        $areas = ParkingArea::all();
        $gates = ['1','2']; 
        return view('administrator.maps-slot', compact('areas', 'gates'));
    }

    public function show(){
         return response()->json(ParkingArea::all());
    }
}
