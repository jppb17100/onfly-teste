<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelOrderController;

Route::get('/', function () {
    return view('welcome');
});


