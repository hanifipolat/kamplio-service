<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'middleware' => ['api'],
    'prefix' => 'auth'
], static function ($router) {
    Route::post('login', [UserController::class,'login']);
    Route::post('register', [UserController::class,'register']);
    Route::group(['middleware' => 'jwt.verify'], static function( $router){

        Route::post('logout', [UserController::class,'logout']);
        Route::post('refresh', [UserController::class,'refresh']);
        Route::get('email-verify', [UserController::class,'emailVerify']);
        Route::get('detail', [UserController::class,'detail']);
        Route::get('create-verify-code', [UserController::class,'createVerifyCode']);
        Route::post('reset-password', [UserController::class,'resetPassword']);
        Route::post('change-password', [UserController::class,'changePassword']);
        Route::post('save-user-interests', [UserController::class,'saveUserInterests']);
    });
});
Route::group([
    'middleware' => ['api']
], static function ($router) {
    Route::group(['middleware' => 'jwt.verify'], static function( $router){
        Route::get('getInterestingPosts', [PostController::class,'showPostList']);

    });
});

