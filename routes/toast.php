<?php

use App\Http\Controllers\ToastExampleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Toast Notification Example Routes
|--------------------------------------------------------------------------
|
| Rute-rute ini digunakan untuk mendemonstrasikan penggunaan notifikasi toast
| dalam aplikasi Laravel.
|
*/

Route::get('/toast-example', [ToastExampleController::class, 'index'])->name('toast.example');
Route::get('/toast-example/success', [ToastExampleController::class, 'success'])->name('toast.success');
Route::get('/toast-example/error', [ToastExampleController::class, 'error'])->name('toast.error');
Route::get('/toast-example/warning', [ToastExampleController::class, 'warning'])->name('toast.warning');
Route::get('/toast-example/info', [ToastExampleController::class, 'info'])->name('toast.info');
Route::get('/toast-example/javascript', [ToastExampleController::class, 'javascript'])->name('toast.javascript');