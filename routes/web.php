<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CategoryController;


//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [CalendarController::class, 'index']);
Route::resource('categories', CategoryController::class);
