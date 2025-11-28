<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManageController extends Controller
{
    //
    public function users(){
        return view('manage.users');
    }

    public function pricing(){
        return view('manage.price');
    }
}
