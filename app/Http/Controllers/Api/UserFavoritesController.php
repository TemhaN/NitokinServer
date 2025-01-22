<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserFavoriteResource;
use App\Models\Favorites;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class UserFavoritesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response(['message' => 'User not found'], 404);
        }

        $favorites = $user->favorites();

        return response(['favorites' => UserFavoriteResource::collection($favorites->get())]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $userId)
    {

        $user = User::find($userId);

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $gameId = $request->input('game_id');
        if (! $gameId) {
            return response()->json(['error' => 'Game ID is required'], 400);
        }

        $gameExists = Game::where('id', $gameId)->exists();
        if (! $gameExists) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        $existingFavorite = Favorites::where('user_id', $userId)
            ->where('game_id', $gameId)
            ->first();

        if ($existingFavorite) {
            return response()->json(['error' => 'Favorite already exists for this user and movie'], 409);
        }

        $favorite = Favorites::create([
            'user_id' => $userId,
            'game_id' => $gameId,
        ]);

        return response(new UserFavoriteResource($favorite), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $userId, $gameId)
    {
        // Проверяем, есть ли пользователь с указанным ID
        $user = User::find($userId);
        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Проверяем, есть ли обязательный параметр game_id в запросе
        if (! $gameId) {
            return response()->json(['error' => 'Game ID is required'], 400);
        }

        // Находим запись избранного фильма для данного пользователя и game_id
        $favorites = Favorites::where('user_id', $userId)
            ->where('game_id', $gameId)
            ->first();

        if (! $favorites) {
            return response()->json(['error' => 'Favorite not found for this user and movie'], 404);
        }

        // Проверяем, может ли текущий пользователь удалить этот избранный фильм
        if ($favorites->user_id != auth()->user()->id) {
            return response()->json(['error' => 'You can only delete your own favorites'], 403);
        }

        // Удаляем запись из таблицы Favorites
        $favorites->delete();

        // Возвращаем успешный ответ
        return response('', 204);
    }
}