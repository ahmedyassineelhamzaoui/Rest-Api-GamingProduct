<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
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



Route::controller(AuthController::class)->group(function(){
    Route::post('register','createUser');
    Route::post('login','login');
    Route::get('logout','logout');
});

Route::controller(UserController::class)->group(function(){
   Route::post('reset-password','sendEmail');
   Route::post('change-password','changePassword');
   Route::delete('close-account','closeAccount');
   Route::put('contact-info','updateProfile');
   Route::get('users','getAllUsers');
   Route::put('edit-user','editUser');
   Route::delete('delete-user','deleteUser');
});

Route::controller(CategorieController::class)->group(function(){
   Route::post('create-categorie','addCategorie');
   Route::delete('delete-categorie','deleteCategorie');
   Route::put('update-categorie','updateCategorie');
});
