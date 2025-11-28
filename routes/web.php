<?php


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QrcodeController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ParkingAreaController;
use App\Http\Controllers\ParkingSlotController;
use App\Http\Controllers\ReservationController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::middleware('can:isAdmin')->group(function(){
        Route::get('/maps-slot', [MapsController::class, 'index'])->name('maps-slot');
        Route::get('/maps-slot/show', [MapsController::class, 'show'])->name('maps-slot.show');
        Route::get('/dashboard', [AdminController::class, 'index'])->name('index');
        Route::get('/dashboard/json', [AdminController::class, 'indexjson'])->name('indexjson');
        Route::get('/booking', [AdminController::class, 'booking'])->name('booking');
        Route::get('/parked',[AdminController::class, 'parked'])->name('parked');
        Route::get('/out',[AdminController::class, 'out'])->name('out');
        Route::get('/users',[AdminController::class, 'users'])->name('users');
        Route::get('/pricing',[AdminController::class, 'price'])->name('price');

        Route::get('/get-slots-by-vehicle/{vehicle_id}', [ReservationController::class, 'getSlotsByVehicle']);



        Route::get('/parking-areas/list', [ParkingAreaController::class, 'index'])->name('parking-areas.index');
        Route::post('/parking-areas', [ParkingAreaController::class, 'store'])->name('parking-areas.store');
        Route::put('/parking-areas/{id}', [ParkingAreaController::class, 'update']);
        Route::delete('/parking-areas/{id}', [ParkingAreaController::class, 'destroy']);

        Route::get('/api/parking-areas', [ParkingAreaController::class, 'list'])->name('api.parking-areas.list');


        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::post('/reservations/store', [ReservationController::class, 'store'])->name('reservations.store');
        Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::get('/scan-in/{token}', [ReservationController::class, 'scan'])->name('scan.in');
        // Route::get('/scan/{token}', [ReservationController::class, 'scan'])->name('reservations.scan');
        // Route::get('/reservations-ajax', [ReservationController::class, 'ajax'])->name('reservations.ajax');

        Route::get('/scan-out/token/{token}', [ParkingController::class, 'scanOutByToken'])->name('scan.out.token');
        Route::get('/parked/json', [ParkingController::class, 'getActiveParkings'])->name('parked.json');
        Route::get('/completed/json', [AdminController::class, 'jsonout'])->name('completed.json');
        Route::get('/reservations/json', [ReservationController::class, 'json'])->name('reservations.json');

        Route::get('/users/data', [AdminController::class, 'getData'])->name('users.data');
        Route::delete('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');


        Route::get('/parking-slots', [ParkingSlotController::class, 'index'])->name('parking-slots.index');
        Route::post('/parking-slots/store', [ParkingSlotController::class, 'store'])->name('parking-slots.store');
        Route::delete('/parking-slots/{id}', [ParkingSlotController::class, 'destroy'])->name('parking-slots.destroy');
        Route::post('/parking-slots/store-multiple', [ParkingSlotController::class, 'storeMultiple'])->name('parking-slots.store-multiple');
    });
});


Route::middleware('auth')->group(function () {
    Route::middleware('can:isUser')->group(function(){

        Route::get('/dashboard-user', [Controller::class, 'indexuser'])->name('index.user');
        // Halaman user
        Route::get('/user/reservasi', [Controller::class, 'index'])->name('user.reservation.index'); 
        Route::get('/user/reservasi/history', [Controller::class, 'historyAjax']);



        // JSON realtime untuk tabel
        Route::get('/user/reservasi/json', [Controller::class, 'json']);


        Route::get('/user/reservasi', [Controller::class,'index']);
        Route::post('/user/reservasi/store-user', [Controller::class,'storeUser']);
        Route::get('/user/reservasi/history', [Controller::class,'historyAjax']);

        // QR Image
        Route::get('/user/reservasi/qrcode/{token}', [Controller::class,'qrcode']);
        Route::get('/user/profile', [Controller::class,'profile'])->name('user.profile');
        Route::post('/user/profile/update', [Controller::class,'updateProfile'])->name('user.profile.update');
        Route::get('/user/profile/delete', [Controller::class,'deleteAccount'])->name('user.profile.delete');

        Route::post('/profile/vehicle/add', [Controller::class, 'store'])->name('vehicle.add');
        Route::delete('/profile/vehicle/{id}/delete', [Controller::class, 'destroy']);

        Route::get('/user/slot-by-vehicle/{id}', [Controller::class, 'getSlotsByVehicle'])->name('user.slot.by.vehicle');

     


    });
});

   // SCAN
        Route::get('/scan-user/{token}', [Controller::class,'scan'])->name('scan.user');
        Route::get('/scanout-user/{token}', [Controller::class,'scanOut'])->name('scanout.user');




Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



