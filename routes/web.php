<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Routes\Route;

// PostController::class;
// //PostController
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/my-post', [PostController::class, 'myPost'])->name('my-post')->middleware('auth');
Route::get('/post-create', [PostController::class, 'create'])->name('post.create')->middleware('auth');
Route::post('/post-store', [PostController::class, 'store'])->name('post.store')->middleware('auth');
Route::get('/post-show/{id}', [PostController::class, 'show'])->name('post.show');
Route::get('/post-edit/{id}', [PostController::class, 'edit'])->name('post.edit')->middleware('auth');
Route::put('/post-update/{id}', [PostController::class, 'update'])->name('post.update')->middleware('auth');
Route::delete('/post-destroy/{id}', [PostController::class, 'destroy'])->name('post.destroy')->middleware('auth');


// //Login, Logout, Register and Profile update
Route::get('/login', [AuthController::class, 'loginView'])->name('login.view')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/register-view', [AuthController::class, 'registerView'])->name('register.view')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('guest');
Route::get('/my-profile', [AuthController::class, 'myProfile'])->name('my-profile')->middleware('auth');
Route::put('/update-profile', [AuthController::class, 'updateProfile'])->name('update-profile')->middleware('auth');