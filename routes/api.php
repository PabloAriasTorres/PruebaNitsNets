<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

//UN GRUPO DE RUTAS CON EL VERSIONADO PERO SIN MIDDLEWARE PARA QUE NO SEA NECESARIO METER UN TOKEN PARA PODER
//ACCEDER A LAS RUTAS
Route::group(['prefix' => 'v1'], function(){
    Route::post('login', [UsuarioController::class, 'login']);
    Route::post('registro', [UsuarioController::class, 'registro']);
});

//OTRO GRUPO DE RUTAS CON EL VERSIONADO Y CON EL MIDDLEWARE PARA RESTRINGIR EL ACCESO Y QUE SOLAMENTE PUEDAN ACCEDER
//USUARIOS LOGEADOS
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers', 'middleware' => 'auth:sanctum'], function(){
    Route::apiResource('usuarios',UsuarioController::class);
    Route::apiResource('deportes',DeporteController::class);
    Route::apiResource('pistas',PistaController::class);
    Route::apiResource('socios',SocioController::class);
    Route::apiResource('reservas',ReservaController::class);
});