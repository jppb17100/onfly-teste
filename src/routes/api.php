<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelOrderController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Grupo para rotas autenticadas
Route::middleware(\App\Http\Middleware\Authenticate::class)->group(function () {
    // Rotas de pedidos de viagem

    Route::apiResource('travel-orders', TravelOrderController::class)
        ->except(['update', 'destroy']);

    Route::patch('/travel-orders/{travel_order}/status',
        [TravelOrderController::class, 'updateStatus'])
        ->name('travel-orders.status');

    Route::patch('/travel-orders/{travel_order}/cancel',
        [TravelOrderController::class, 'cancel'])
        ->name('travel-orders.cancel');
});
