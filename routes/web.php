<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
//
// Routes
//

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/test', function () {
//     return view('client');
// });

Route::get('/test', [AdminController::class, 'test2'])->name('test');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix'=> 'admin', 'middleware'=>['isAdmin', 'auth', 'PreventBackHistory']], function(){

    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/category', [AdminController::class, 'add_category'])->name('admin.category');
    Route::get('/category/{id}', [AdminController::class, 'category']);
    Route::post('/ecategory', [AdminController::class, 'ecategory'])->name('admin.ecategory');
    Route::get('/del_cat/{id}', [AdminController::class, 'del_category'])->name('admin.del_cat');




    Route::get('/images/{id}', [AdminController::class, 'images'])->name('admin.images');
    Route::post('image', [AdminController::class, 'add_image'])->name('admin.image');
    Route::get('/del_img/{id}', [AdminController::class, 'del_img'])->name('admin.del_img');




});




































Route::group(['prefix'=> 'agent', 'middleware'=>['isAgent', 'auth', 'PreventBackHistory']], function(){

    Route::get('/dashboard', [AgentController::class, 'index'])->name('agent.dashboard');
});

Route::group(['prefix'=> 'user', 'middleware'=>['isUser', 'auth', 'PreventBackHistory']], function(){

    Route::get('dashboard', [UserController::class, 'index'])->name('user.dashboard');


});
