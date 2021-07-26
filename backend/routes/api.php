<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
// 会員登録
Route::post('/register', [Auth\RegisterController::class, 'register'])->name('register');
Route::post('/login', [Auth\LoginController::class, 'login'])->name('login');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
