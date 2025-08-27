<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register');
Route::post('/login');
Route::post('/refresh');
Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('posts')->group(function(){
        Route::get("/");
        Route::post("/");
        Route::get("/{post}");
        Route::put("/{post}");
        Route::delete("/{post}");
        Route::get("/{post}/bookmarks");
    });

    Route::prefix('users')->group(function(){
        Route::get('/');
        Route::get('/{user}');
        Route::get("/{user}/posts");
        Route::put("/{user}");
    });
});