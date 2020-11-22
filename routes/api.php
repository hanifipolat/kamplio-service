<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController as MemberController;

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
    'middleware' => ['jwt.verify'],
    'prefix' => 'member'
], static function ($router) {
    Route::post('login', [MemberController::class,'login']);
    Route::post('register', [MemberController::class,'register']);
    Route::group(['middleware' => 'jwt.verify'], static function( $router){
        Route::post('logout', [MemberController::class,'logout']);
        Route::post('refresh', [MemberController::class,'refresh']);
        Route::get('detail', [MemberController::class,'detail']);
    });
});
