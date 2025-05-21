<?php

use App\Http\Controllers\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;

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

//this for favorite controller
//rout for show all favorite
Route::get('/favorites', [FavoriteController::class, 'getAllFavorites']);
//rout for show favorite by id
Route::get('/favorites/{id}', [FavoriteController::class, 'getFavoriteById']);
//route for delete favorite
Route::delete('/favorites/{id}', [FavoriteController::class, 'deleteFavoriteById']);

//this for purchase controller
//route for show all purchases
Route::get('/purchases', [PurchaseController::class, 'getAllPurchases']);
//route for show  purchases by id
Route::get('/purchases/{id}', [PurchaseController::class, 'getPurchaseById']);

//this for profile controller
//rout for show profile by user id
Route::get('/profile/{userId}', [ProfileController::class, 'getProfileByUid'])->middleware('auth:sanctum');
//rout for update  profile information by user id
Route::put('/profile', [ProfileController::class, 'updateProfileInfo'])->middleware('auth:sanctum');
//rout for delete  profile
Route::delete('/profile/picture', [ProfileController::class, 'removeProfilePicture'])->middleware('auth:sanctum');


