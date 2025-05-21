<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyBuyerController;
use App\Http\Controllers\BuyerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/users', [UserController::class, 'getallUsers']);
Route::get('/users/{id}', [UserController::class, 'getUserById']);
Route::post('/users', [UserController::class, 'createUser']);
Route::put('/users/{id}', [UserController::class, 'updateUser']);
Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
Route::get('/users/{keyword}', [UserController::class, 'searchUsers']);
Route::get('/properties', [PropertyBuyerController::class, 'getAllProperties']);
//البحث
Route::get('/properties/search', [PropertyBuyerController::class, 'search']);
//البحث عن وكيل
Route::get('/agents/search/{keyword}', [BuyerController::class, 'searchAgents']);
//تفاصيل العقار
Route::get('/properties/{id}', [PropertyBuyerController::class, 'show']);
//كل الوكلاء
Route::get('/agents', [BuyerController::class, 'getAllAgents']);
//تفاصيل الوكيل
Route::get('/agents/{id}', [BuyerController::class, 'getAgentById']);
