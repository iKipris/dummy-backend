<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\UploadFileToS3Controller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/validate-token', [AuthController::class, 'validateToken']);


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/settings', [AiController::class, 'index']);
    Route::post('/settings', [AiController::class, 'store']);
    Route::post('/listings', [ListingController::class, 'store']);
    Route::get('/listings', [ListingController::class, 'index']);
    Route::post('/listings/publish', [ListingController::class, 'publish']);
    Route::post('/listings/preferences', [ListingController::class, 'storePreferences']);
    Route::get('/listings/preferences', [ListingController::class, 'indexPreferences']);
    Route::get('/analytics/marketing', [AnalyticsController::class, 'indexMarketing']);
    Route::get('/analytics/general', [AnalyticsController::class, 'indexGeneral']);
    Route::get('/calendar/events', [CalendarController::class, 'index']);
    Route::post('/calendar/events', [CalendarController::class, 'store']);
    Route::post('/calendar/events/{id}', [CalendarController::class, 'update']);
    Route::delete('/calendar/events/{id}', [CalendarController::class, 'delete']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/upload/file', [UploadFileToS3Controller::class, 'upload']);
    Route::post('/ai/add/message', [AiController::class, 'addMessage']);
    Route::post('/ai/chat/store', [AiController::class, 'storeChat']);
    Route::get('/ai/chats', [AiController::class, 'index']);
});
