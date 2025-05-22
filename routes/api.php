<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

use App\Http\Controllers\PropertyBuyerController;
use App\Http\Controllers\BuyerController;

use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManageReviewController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NegotiationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AgentStatsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PropertyImageController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
Route::post('/verify2FA', [AuthController::class, 'verify2FA']);
Route::post('/resetPassword', [AuthController::class, 'reset']);


//Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'getAllUsersAdmin']);
    Route::put('/admin/approve/{id}', [AdminController::class, 'ApproveUserRequest']);
    Route::put('/admin/reject/{id}', [AdminController::class, 'RejectUserRequest']);
    Route::get('/admin/search/{keyword}', [AdminController::class, 'SearchUserRequest']);
    Route::post('/admin', [AdminController::class, 'AddUserAdmin']);
    Route::delete('/admin/{id}', [AdminController::class, 'DeleteUserAdmin']);
    Route::get('/admin/properties', [AdminController::class, 'getAllPropertiesAdmin']);
    Route::get('/admin/properties/search/{keyword}', [AdminController::class, 'SearchPropertyRequest']);
    Route::delete('/admin/properties/{id}', [AdminController::class, 'DeletePropertyAdmin']);
    Route::get('/reviews/search/{keyword}', [ManageReviewController::class, 'searchReviews']);
    Route::get('/reviews/getAllReviews', [ManageReviewController::class, 'getAllReviews']);
    Route::delete('/reviews/{id}', [ManageReviewController::class, 'deleteReview']);
});
// Buyer routes
Route::middleware(['auth:sanctum','buyer'])->prefix('buyer')->group(function () {
    Route::post('/negotiations/propose', [NegotiationController::class, 'propose']);
    Route::post('/reviews', [ReviewController::class, 'storeReview']);
    Route::get('/properties', [PropertyBuyerController::class, 'getAllProperties']);
    Route::get('/properties/search', [PropertyBuyerController::class, 'search']);
    Route::get('/agents/search/{keyword}', [BuyerController::class, 'searchAgents']);
    Route::get('/properties/{id}', [PropertyBuyerController::class, 'show']);
    Route::get('/agents', [BuyerController::class, 'getAllAgents']);
    Route::get('/agents/{id}', [BuyerController::class, 'getAgentById']);
    Route::get('/purchases', [PurchaseController::class, 'getAllPurchases']);
    Route::get('/purchases/{keyword}', [PurchaseController::class, 'searchPurchase']);
    Route::get('/favorites', [FavoriteController::class, 'getAllFavorites']);
    Route::post('/favorites', [FavoriteController::class, 'addFavorite']);
    Route::delete('/favorites', [FavoriteController::class, 'deleteFavorite']);
});


Route::middleware(['auth:sanctum','agent'])->prefix('agent')->group(function () {
    Route::get('/negotiations', [NegotiationController::class, 'received']);
    Route::put('/negotiations/{id}/accept', [NegotiationController::class, 'acceptNegotiation']);
    Route::put('/negotiations/{id}/reject', [NegotiationController::class, 'rejectNegotiation']);
    Route::post('/reviews/reply', [ReviewController::class, 'storeReplay']);
    Route::get('/reviews', [ReviewController::class, 'myReviews']);
    Route::get('/property-stats', [AgentStatsController::class, 'getPropertyStats']);




});



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfileByUid']);
    Route::put('/profile/update', [ProfileController::class, 'updateProfileInfo']);
    Route::delete('/profile/remove-picture', [ProfileController::class, 'removeProfilePicture']);
});



Route::post('messages', [MessageController::class, 'store']);
Route::get('messages/{id}', [MessageController::class, 'show']);
Route::put('messages/{id}', [MessageController::class, 'update']);
Route::delete('messages/{id}', [MessageController::class, 'destroy']);








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
Route::delete('property-images/{id}', [PropertyImageController::class,'destroy']);
