<?php

use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\AuthorsController;
use App\Http\Controllers\Api\V1\AuthorTicketsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware(['auth:sanctum'])->apiResource('tickets', TicketController::class);
Route::middleware(['auth:sanctum'])->apiResource('authors', AuthorsController::class);
Route::middleware(['auth:sanctum'])->apiResource('authors.tickets', AuthorTicketsController::class);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user(); 
    });
});