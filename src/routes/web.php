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

//ログイン
Route::middleware('auth', 'verified')->group(function () {
  Route::get('/', [AuthController::class, 'index']);
});

//メール認証
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//ログアウト
Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

//打刻
Route::group(['middleware' => 'auth'], function() {
  Route::post('/stamp', [AttendanceController::class, 'stamp']);
});

//日付一覧ページ
Route::middleware('auth')->group(function () {
  Route::get('/date', [AttendanceController::class, 'date']);
});

//日付一覧ページ（日付の変更）
Route::middleware('auth')->group(function () {
  Route::get('/changeDate', [AttendanceController::class, 'changeDate']);
});

//ユーザー一覧ページ
Route::middleware('auth')->group(function () {
  Route::get('/list', [AttendanceController::class, 'list']);
});

//ユーザー毎勤怠表ページ
Route::middleware('auth')->group(function () {
  Route::post('/user', [AttendanceController::class, 'user']);
});

//ユーザー毎勤怠表ページ（表示月の変更）
Route::middleware('auth')->group(function () {
  Route::get('/userChangeMonth', [AttendanceController::class, 'userChangeMonth']);
});
