<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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
Route::middleware('auth', 'verified')->group(function () {
  Route::get('/', [AuthController::class, 'index']);
});

//検証中
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//検証中
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

//検証中
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::group(['middleware' => 'auth'], function() {
  Route::post('/stamp', [AttendanceController::class, 'stamp']);
});

Route::middleware('auth')->group(function () {
  Route::get('/date', [AttendanceController::class, 'date']);
});

Route::middleware('auth')->group(function () {
  Route::get('/changeDate', [AttendanceController::class, 'changeDate']);
});

Route::middleware('auth')->group(function () {
  Route::get('/list', [AttendanceController::class, 'list']);
});

Route::middleware('auth')->group(function () {
  Route::get('/user', [AttendanceController::class, 'user']);
});


Route::middleware('auth')->group(function () {
  Route::post('/user', [AttendanceController::class, 'user']);
});

Route::middleware('auth')->group(function () {
  Route::get('/userChangeMonth', [AttendanceController::class, 'userChangeMonth']);
});
