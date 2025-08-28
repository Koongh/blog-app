<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function(){
    Route::get("/version", function(){
        return response()->json(['message'=> '1.0.0']);
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail']);
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);
    Route::post('/refresh');

    Route::get('/posts', [PostController::class, 'index']);      
    Route::get('/posts/{post}', [PostController::class, 'show']);  
    Route::get('/users/{user}/posts', [PostController::class, 'userPosts']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    });

    Route::get('/users/{user}', [UserController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/posts', [PostController::class, 'store']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::get('/bookmarks', [BookmarkController::class, 'index']);       
        Route::post('/bookmarks/{post}', [BookmarkController::class, 'store']);  
        Route::delete('/bookmarks/{post}', [BookmarkController::class, 'destroy']); 
        Route::get('/subscriptions', [SubscriptionController::class, 'mySubscriptions']);
        Route::get('/subscribers', [SubscriptionController::class, 'mySubscribers']);     
        Route::post('/subscribe/{user}', [SubscriptionController::class, 'subscribe']); 
        Route::delete('/unsubscribe/{user}', [SubscriptionController::class, 'unsubscribe']); 
    });
});