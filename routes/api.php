<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\InscricaoController;
use Illuminate\Support\Facades\Route;

// Autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas públicas de eventos
Route::get('/eventos', [EventoController::class, 'index']);
Route::get('/eventos/{evento}', [EventoController::class, 'show']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/eventos', [EventoController::class, 'store']);

    Route::match(['put', 'patch'], '/eventos/{evento}', [EventoController::class, 'update']); 

    Route::delete('/eventos/{evento}', [EventoController::class, 'destroy']);

    // Inscrições
    Route::get('/minhas-inscricoes', [InscricaoController::class, 'index']);
    Route::post('/eventos/{evento}/inscricoes', [InscricaoController::class, 'store']);
    Route::delete('/inscricoes/{inscricao}', [InscricaoController::class, 'destroy']);

});