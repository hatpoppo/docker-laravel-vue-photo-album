<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
//写真投稿
Route::post('/photos', [App\Http\Controllers\PhotoController::class, 'create'])->name('photo.create');
//写真一覧
Route::get('/photos', [App\Http\Controllers\PhotoController::class, 'index'])->name('photo.index');
//写真詳細
Route::get('/photos/{id}', [App\Http\Controllers\PhotoController::class, 'show'])->name('photo.show');
//コメント
Route::post('/photos/{photo}/comments', [App\Http\Controllers\PhotoController::class, 'addComment'])->name('photo.comment');
// ログインユーザー
Route::get('/user', fn () => Auth::user())->name('user');
