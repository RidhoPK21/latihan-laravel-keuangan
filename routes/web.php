<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::group(['prefix' => 'app', 'middleware' => 'check.auth'], function () {
    // MODIFIKASI: Pastikan hanya ini yang ada di grup 'app'
    Route::get('/home', [HomeController::class, 'index'])->name('app.home');

    // BARU: Tambahkan rute untuk detail transaksi
    Route::get('/transactions/{transaction_id}', [HomeController::class, 'transactionDetail'])->name('app.transactions.detail');
});

Route::get('/', function () {
    return redirect()->route('app.home');
});