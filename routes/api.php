<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfessionalController;
use App\Http\Controllers\Api\LocationController;

// Routes عامة (لا تحتاج توكن)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// خدمات عامة
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/professionals', [ProfessionalController::class, 'index']);
Route::get('/professionals/top-rated', [ProfessionalController::class, 'topRated']);
Route::get('/professionals/{id}', [ProfessionalController::class, 'show']);
Route::get('/professionals/{id}/ratings', [ProfessionalController::class, 'getRatings']);
Route::get('/professionals/{id}/services', [ServiceController::class, 'getProfessionalServices']);
Route::post('/professionals/{professionalId}/services/attach', 
    [ServiceController::class, 'attachServiceToProfessional']
);
Route::delete('/professionals/{professionalId}/services/detach', 
    [ServiceController::class, 'detachServiceFromProfessional']
);
// Routes محمية (تحتاج توكن)
Route::middleware('auth:sanctum')->group(function () {
    
    // المصادقة والمستخدم
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/professional-details', [AuthController::class, 'updateProfessionalDetails']);
    
    // المواقع
    Route::apiResource('locations', LocationController::class);
    
    // الطلبات
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/my-customer', [OrderController::class, 'myOrdersAsCustomer']);
    Route::get('/orders/my-professional', [OrderController::class, 'myOrdersAsProfessional']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/rate', [OrderController::class, 'rateOrder']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
    
    // الإشعارات
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});

// Routes للمدير فقط (اختياري)
Route::middleware(['auth:sanctum', 'user.type:admin'])->group(function () {
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
});