<?php

use App\Http\Controllers\Admin\ActorController;
use App\Http\Controllers\Admin\ActorGameController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryGameController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\Easter_EggController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\RatingGameController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PolicyController as AdminPolicyController;

Route::middleware(['guest:admin'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login_process');
});

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/', [AuthController::class, 'updateAdmin'])->name('admin.update');




    // Route::prefix('admin')->name('admin.')->group(function () {
    // });

    Route::resource('/policies', AdminPolicyController::class);
    // Route::resource('/policies', AdminPolicyController::class);

    Route::resource('/countries', CountryController::class)->except(['show']);
    Route::resource('/categories', CategoryController::class)->except(['show']);
    Route::resource('/games', GameController::class)->except(['show']);
    Route::resource('categorygames', CategoryGameController::class);
    Route::resource('actorgames', ActorGameController::class);

    Route::resource('/reviews', ReviewController::class);
    Route::resource('/ratings', RatingGameController::class);
    Route::resource('/actors', ActorController::class);

    Route::resource('/easter_egg', Easter_EggController::class);

    Route::patch('/categorygame/{id}', [CategoryGameController::class, 'update'])->name('categorygame.update');
    Route::patch('/actorgame/{id}', [ActorGameController::class, 'update'])->name('actorgame.update');

    Route::get('/gameinfo/{game_id}', [ReviewController::class, 'show'])->name('gameinfo.show');
    Route::get('/gameinfo', [ReviewController::class, 'show'])->name('admins.games.show');

    Route::patch('gameinfo/{game_id}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('gameinfo/{game_id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::patch('gameinfo/{game_id}/toggle', [ReviewController::class, 'toggle'])->name('reviews.toggle');

    Route::resource('/users', UserController::class)->except(['destroy']);
    Route::resource('/users', UserController::class)->except(['show']);
    Route::get('/admins/users', [UserController::class, 'index'])->name('admins.users.index');
    Route::delete('users/{user}/ban', [UserController::class, 'adminban'])->name('admins.users.ban');
    Route::put('users/{user}/restore', [UserController::class, 'adminrestore'])->name('admins.users.restore');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('admins.users.update');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
});