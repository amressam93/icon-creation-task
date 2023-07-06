<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (){
    return view('welcome');
})->name('welcome');

Route::prefix('register')->group(function () {
    Route::get('', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('', [RegisterController::class, 'register']);
});

Route::prefix('login')->group(function () {
    Route::get('', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('', [LoginController::class, 'login']);
});
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [LoginController::class, 'dashboard'])->name('dashboard');
    Route::get('users', [UserController::class, 'list'])->name('users');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
