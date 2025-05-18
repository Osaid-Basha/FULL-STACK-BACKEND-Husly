<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/users', [UserController::class, 'getallUsers']);
Route::get('/users/{id}', [UserController::class, 'getUserById']);
Route::post('/users', [UserController::class, 'createUser']);
Route::put('/users/{id}', [UserController::class, 'updateUser']);
Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
Route::get('/users/{keyword}', [UserController::class, 'searchUsers']);
Route::get('/properties', [PropertyController::class, 'getAllProperties']);

//Admin Routes

    Route::get('/admin/users', [AdminController::class, 'getAllUsersAdmin']);
    Route::put('/admin/users/approve/{id}', [AdminController::class, 'approveUserRequest']);
    Route::put('/admin/users/reject/{id}', [AdminController::class, 'rejectUserRequest']);
    Route::get('/admin/users/search/{keyword}', [AdminController::class, 'searchUserRequest']);
    Route::post('/admin/users', [AdminController::class, 'AddUserAdmin']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'DeleteUserAdmin']);



