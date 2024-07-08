<?php

use Illuminate\Support\Facades\Route;

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


Auth::routes();
Route::get('/csrf-token', function() {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::group(['namespace'=> 'Auth'], function () {

 

    Route::get('dang-nhap',[App\Http\Controllers\Auth\LoginController::class, 'getLogin'])->name('get.login');
    Route::post('dang-nhap',[App\Http\Controllers\Auth\LoginController::class, 'postLogin'])->name('post.login');

    Route::get('dang-xuat',[App\Http\Controllers\Auth\LoginController::class, 'getLogout'])->name('get.logout.user');


});


Route::get('/register', function () {
    return view('welcome');
});


