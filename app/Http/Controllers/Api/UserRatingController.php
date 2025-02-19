<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRatingRequest;
use App\Http\Resources\UserRatingResource;
use App\Models\Rating;
use App\Models\User;

class UserRatingController extends Controller
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
        $ratings = $user->ratings();

        return response(['ratings' => UserRatingResource::collection($ratings->get())]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRatingRequest $request)
    {

        $userId = $request->input('user_id');
        $gameId = $request->input('game_id');
        $ball = $request->input('ball');

        if (! $userId || ! $gameId || ! $ball) {
            return response()->json(['error' => 'User ID, Game ID, and Score are required'], 400);
        }

        $existingRating = Rating::where('user_id', $userId)
            ->where('game_id', $gameId)
            ->first();

        if ($existingRating) {
            $existingRating->update(['ball' => $ball]);

            return response(new UserRatingResource($existingRating), 200);
        }

        $rating = Rating::create([
            'user_id' => $userId,
            'game_id' => $gameId,
            'ball' => $ball,
        ]);

        return response(new UserRatingResource($rating), 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($userId, $ratingId)
    {
        $user = User::findOrFail($userId);
        $rating = $user->ratings()->findOrFail($ratingId);

        if ($rating->user_id != auth()->user()->id) {
            return response(['error' => 'You can only delete your own ratings'], 403);
        }

        $rating->delete();

        return response('', 204);
    }
}