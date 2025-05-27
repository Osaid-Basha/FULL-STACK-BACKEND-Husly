<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

use App\Http\Controllers\PropertyBuyerController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\BuyingRequestController;

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
use App\Http\Controllers\NotificationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);//done
Route::post('/login', [AuthController::class, 'login']);//done
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');//done
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);//done
Route::post('/verify2FA', [AuthController::class, 'verify2FA']);//done
Route::post('/resetPassword', [AuthController::class, 'reset']);//done


//Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'getAllUsersAdmin']);//done
    Route::put('/admin/approve/{id}', [AdminController::class, 'ApproveUserRequest']);//done
    Route::put('/admin/reject/{id}', [AdminController::class, 'RejectUserRequest']);//done
    Route::get('/admin/search/{keyword}', [AdminController::class, 'SearchUserRequest']);//done
    Route::post('/admin', [AdminController::class, 'AddUserAdmin']);//done
    Route::delete('/admin/{id}', [AdminController::class, 'DeleteUserAdmin']);//done
    Route::get('/admin/properties', [AdminController::class, 'getAllPropertiesAdmin']);//done
    Route::get('/admin/properties/{id}', [AdminController::class, 'getPropertyById']);//done
    Route::get('/admin/properties/search/{keyword}', [AdminController::class, 'SearchPropertyRequest']);//done
    Route::delete('/admin/properties/{id}', [AdminController::class, 'DeletePropertyAdmin']);//done
    Route::get('/admin/user/pending' , [AdminController::class, 'getPendingUsers']);//done
    Route::get('admin/user/stats', [AdminController::class, 'getStatisticsAdmin']);//done
    Route::get('admin/reviews/search/{keyword}', [ManageReviewController::class, 'searchReviews']);//done
    Route::get('admin/reviews/getAllReviews', [ManageReviewController::class, 'getAllReviews']);//done
    Route::delete('admin/reviews/{id}', [ManageReviewController::class, 'deleteReview']);//done
});
// Buyer routes
Route::middleware(['auth:sanctum','buyer'])->prefix('buyer')->group(function () {
    Route::post('/negotiations/propose', [NegotiationController::class, 'propose']);//done
    Route::post('/buying-requests/confirm/{id}', [BuyingRequestController::class, 'confirm']);//done
    Route::post('/reviews', [ReviewController::class, 'storeReview']);//done
    Route::get('/properties', [PropertyBuyerController::class, 'getAllProperties']);//done
    Route::get('/properties/search', [PropertyBuyerController::class, 'search']);//done
    Route::get('/agents/search/{keyword}', [BuyerController::class, 'searchAgents']);//done
    Route::get('/properties/{id}', [PropertyBuyerController::class, 'show']);//done
    Route::get('/agents', [BuyerController::class, 'getAllAgents']);//done
    Route::get('/agents/{id}', [BuyerController::class, 'getAgentById']);//done
    Route::get('/purchases', [PurchaseController::class, 'getAllPurchases']);//done
    Route::get('/purchases/{keyword}', [PurchaseController::class, 'searchPurchase']);//done
    Route::get('/favorites', [FavoriteController::class, 'getAllFavorites']);//done
    Route::post('/favorites', [FavoriteController::class, 'addFavorite']);//done
    Route::delete('/favorites', [FavoriteController::class, 'deleteFavorite']);//done
});


Route::middleware(['auth:sanctum','agent'])->prefix('agent')->group(function () {
    Route::get('/negotiations', [NegotiationController::class, 'received']);//done
    Route::put('/negotiations/{id}/accept', [NegotiationController::class, 'acceptNegotiation']);//done
    Route::put('/negotiations/{id}/reject', [NegotiationController::class, 'rejectNegotiation']);//done
    Route::post('/reviews/reply', [ReviewController::class, 'storeReplay']);//done
    Route::get('/reviews', [ReviewController::class, 'myReviews']);//done
    Route::get('/property-stats', [AgentStatsController::class, 'getPropertyStats']);//done
    Route::get('/properties', [PropertyController::class, 'getAllProperties']);//done
    Route::post('properties', [PropertyController::class, 'newProperty']);//done
    Route::get('properties/{id}', [PropertyController::class, 'viewProperty']);//done
    Route::put('properties/{id}', [PropertyController::class, 'updateProperty']);//done
    Route::delete('properties/{id}', [PropertyController::class, 'deleteProperty']);//done

});



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfileByUid']);//done
    Route::put('/profile/update', [ProfileController::class, 'updateProfileInfo']);//done
    Route::delete('/profile/remove-picture', [ProfileController::class, 'removeProfilePicture']);//done
    Route::post('/messages/send', [MessageController::class, 'send']);//done
    Route::get('/messages/{userId}', [MessageController::class, 'conversation']);//done
    Route::get('/chat/list', [MessageController::class, 'chatList']);//done
    Route::get('/notifications', [NotificationController::class, 'myNotifications']);
    Route::put('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{notificationId}', [NotificationController::class, 'deleteNotification']);
});















