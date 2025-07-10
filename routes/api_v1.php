<?php

use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware(['auth:sanctum'])->apiResource('tickets', TicketController::class);
Route::middleware(['auth:sanctum'])->apiResource('users', UsersController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user(); 
    });
});