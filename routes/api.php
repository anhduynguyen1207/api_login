<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResetPasswordController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/refresh-token', [AuthController::class, 'refreshToken']);

Route::middleware('auth:sanctum')->get('/getAll', [AuthController::class, 'getAll']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/todos', TodoController::class);
    Route::apiResource('/users', UserController::class);
    Route::post('/change-pass', [UserController::class,'changePassWord']);
    
});
Route::middleware('refresh.token')->group(function () {
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});

Route::post('reset-password', [ResetPasswordController::class,'sendMail']);

Route::post('change-password/{token}', [ResetPasswordController::class,'reset']);

