<?php

use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;

use App\Http\Controllers\PropertyImageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/users', [UserController::class, 'getallUsers']);
Route::get('/users/{id}', [UserController::class, 'getUserById']);
Route::post('/users', [UserController::class, 'createUser']);
Route::put('/users/{id}', [UserController::class, 'updateUser']);
Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
Route::get('/users/{keyword}', [UserController::class, 'searchUsers']);
//MESSAGE
Route::post('messages', [MessageController::class, 'store']);
Route::get('messages/{id}', [MessageController::class, 'show']);
Route::put('messages/{id}', [MessageController::class, 'update']);
Route::delete('messages/{id}', [MessageController::class, 'destroy']);

//PROPERTY
Route::get('/properties', [PropertyController::class, 'index']);
Route::post('properties', [PropertyController::class, 'store']);
Route::get('properties/{id}', [PropertyController::class, 'show']);
Route::put('properties/{id}', [PropertyController::class, 'update']);
Route::delete('properties/{id}', [PropertyController::class, 'destroy']);
Route::get('/properties/{id}/amenities', [PropertyController::class, 'getAmenities']);


//PROPERTY IMAGE
Route::get('property-images', [PropertyImageController::class, 'index']);
Route::post('property-images', [PropertyImageController::class, 'store']);
Route::get('property-images/{id}', [PropertyImageController::class, 'show']);
Route::put('property-images/{id}', [PropertyImageController::class, 'update']);
Route::delete('property-images/{id}', [PropertyImageController::class, 'destroy']);


