<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\GenderController;
use App\Http\Controllers\Api\MainController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserFavoritesController;
use App\Http\Controllers\Api\UserLikeReviewController;
use App\Http\Controllers\Api\UserRatingController;
use App\Http\Controllers\Api\UserReviewsController;
use App\Http\Controllers\Api\GoogleController;
use App\Http\Controllers\Api\PolicyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// // Маршрут для проверки файла Google
// Route::get('/api/v1/google/callback/', function () {
//     return response()->file(public_path('google08506e80a63a0f02.html'));
// });


Route::get('/verify-email', [AuthController::class, 'verifyEmail']);

Route::get('/games', [GameController::class, 'index']);
Route::get('/game/{id}', [GameController::class, 'show']);
Route::get('/game/{gameId}/reviews', [GameController::class, 'reviews']);
Route::get('/game/{gameId}/favorites', [GameController::class, 'favorites']);
Route::get('/game/{gameId}/actors', [GameController::class, 'actors']);

Route::get('/topRatedGameLink', [MainController::class, 'getTopRatedGameLink']);
Route::get('/getTopRatedGameList', [MainController::class, 'getTopRatedGameList']);

// Route::get('/review/{reviewId}/likes', [UserReviewsController::class, 'index']);

Route::get('/categories', CategoryController::class);
Route::get('/countries', CountryController::class);
Route::get('/genders', GenderController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/google/connect', [GoogleController::class, 'connect'])->name('google.connect');
Route::get('/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('policies/all-latest', [PolicyController::class, 'getAllLatest']);


Route::post('/send-recovery-code', [AuthController::class, 'sendRecoveryCode']);
Route::post('/verify-recovery-code', [AuthController::class, 'verifyRecoveryCode']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/signout', [AuthController::class, 'signout']);

    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::delete('/user', [UserController::class, 'destroy']);

    Route::get('/user/{userId}/reviews', [UserReviewsController::class, 'index']);
    Route::delete('/user/{userId}/review/{reviewId}', [UserReviewsController::class, 'destroy']);

    Route::get('/user/{userId}/ratings', [UserRatingController::class, 'index']);
    Route::delete('/user/{userId}/rating/{ratingId}', [UserRatingController::class, 'destroy']);

    Route::get('/user/{userId}/favorites', [UserFavoritesController::class, 'index']);
    Route::delete('/user/{userId}/favorite/{gameId}', [UserFavoritesController::class, 'destroy']);



    Route::middleware('auth:sanctum')->group(function () {
        Route::get('policies/latest', [PolicyController::class, 'latest']);
        Route::post('policies/accept', [PolicyController::class, 'accept']);
    });

    Route::middleware(['check.id'])->group(function () {

        Route::post('/user/{userId}/reviews', [UserReviewsController::class, 'store']);

        Route::post('/user/{userId}/ratings', [UserRatingController::class, 'store']);

        Route::post('/user/{userId}/favorites', [UserFavoritesController::class, 'store']);

        Route::post('/user/{userId}/review', [UserLikeReviewController::class, 'store']);

    });
});

Route::patch('users/{user}', [UserController::class, 'update'])->name('admins.users.update');