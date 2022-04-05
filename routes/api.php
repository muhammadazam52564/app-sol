<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MainController;


//
//  Auth Controllers
//
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/verify_otp', [AuthController::class, 'VerfiyOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/update_profile_image', [AuthController::class, 'update_profile_image']);
Route::post('/update_profile_image_by_parts', [AuthController::class, 'update_profile_image_by_parts']);
Route::post('/update_club_logo_by_parts', [AuthController::class, 'update_club_logo_by_parts']);
Route::post('/update_club_logo', [AuthController::class, 'update_club_logo']);
Route::post('/update_profile', [AuthController::class, 'update_profile']);
Route::get('/profile/{id}', [AuthController::class, 'get_profile']);
Route::post('/forgot_password', [AuthController::class, 'forgot_password']);
Route::post('/verify_code', [AuthController::class, 'verify_code']);
Route::post('/verify_code', [AuthController::class, 'verify_code']);
Route::post('/set_password', [AuthController::class, 'set_password']);
Route::post('/change_password', [AuthController::class, 'change_password']);
Route::post('/signout', [AuthController::class, 'signout']);
Route::post('/update_profile_recruter', [AuthController::class, 'update_profile_recruter']);


//
//  Other Controllers
//
Route::post('/follow', [MainController::class, 'follow']);
Route::post('/unfollow', [MainController::class, 'unfollow']);
Route::get('/allplayers', [MainController::class, 'allplayers']);
Route::get('/latestplayer/{gender}', [MainController::class, 'latestplayer']);
Route::get('/playergender/{gender}', [MainController::class, 'playergender']);
Route::post('/player_position', [MainController::class, 'player_position']);
Route::get('/player_country/{country}', [MainController::class, 'player_country']);
Route::get('/player_details/{id}', [MainController::class, 'singleplayer']);

Route::get('/recruiters', [MainController::class, 'recruiters']);
Route::post('/upload_video', [MainController::class, 'upload_video']);
Route::get('/my_video/{id}', [MainController::class, 'my_video']);
Route::get('/follow_video/{id}', [MainController::class, 'follow_video']);
Route::post('/like', [MainController::class, 'like']);
Route::post('/dislike', [MainController::class, 'dislike']);



