<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
//Route::post('sendResetLink', [UserController::class, 'sendResetLink']);
//Route::post('reset', [UserController::class, 'reset']);
//Route::post('verify2FA', [UserController::class, 'verify2FA']);






Route::get('/users', [UserController::class, 'getallUsers']);
Route::get('/users/{id}', [UserController::class, 'getUserById']);
Route::post('/users', [UserController::class, 'createUser']);
Route::put('/users/{id}', [UserController::class, 'updateUser']);
Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
Route::get('/users/{keyword}', [UserController::class, 'searchUsers']);
Route::get('/properties', [PropertyController::class, 'getAllProperties']);

Route::post('register', [UserController::class, 'register']);

