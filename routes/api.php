<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

Route::group([

    'middleware' => 'api',

], function ($router) {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    
    
    Route::post('create_post', [PostController::class, 'create_post']);
    Route::get('posts', [PostController::class, 'posts']);
    Route::get('post_details/{id?}', [PostController::class, 'post_details']);
    Route::delete('post_delete/{id?}', [PostController::class, 'post_delete']);
    Route::put('admin/post_approve/{id?}', [PostController::class, 'post_approve']);
});
