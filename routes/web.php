<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("notice/admin/verify", function () {
    return view('notice');
})
    ->middleware(["throttle:6,1", "auth:" . config('filament.auth.guard')])
    ->name("admin.verification.notice");
