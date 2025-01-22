<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActorsResource;
use App\Http\Resources\GameResource;
use App\Http\Resources\ReviewResource;
use App\Models\Actor;
use App\Models\ActorGame;
use App\Models\Favorites;
use App\Models\Game;
use App\Models\Likes;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $size = $request->query('size', 30);
        $sortBy = $request->query('sortBy', 'name');
        $sortDir = $request->query('sortDir', 'asc');

        if (! in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        $games = Game::query();

        if ($sortBy === 'name') {
            $games = $games->orderBy($sortBy, $sortDir);
        } elseif ($sortBy === 'year') {
            $games = $games->orderBy('year_of_issue', $sortDir);
        } elseif ($sortBy === 'rating') {
            $games = $games
                ->withAvg('ratings', 'ball')
                ->orderBy('ratings_avg_ball', $sortDir);
        }

        if ($request->has('search')) {
            $games = $games->where('name', 'like', '%'.$request->query('search').'%');
        }
        if ($request->has('country')) {
            $games = $games->where('country_id', $request->query('country'));
        }
        if ($request->has('category')) {
            $categories = explode('%', $request->query('category'));

            $games = $games->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            });
        }

        $games = $games->paginate($size);

        return response([
            'page' => $games->currentPage(),
            'size' => $games->perPage(),
            'total' => $games->total(),
            'games' => GameResource::collection($games),
        ]);
    }

    public function show($id)
    {
        $game = Game::find($id);

        if (! $game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        return response(new GameResource($game));
    }

    public function reviews($gameId, Request $request)
    {
        $game = Game::find($gameId);

        if (! $game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        $reviews = $game->reviews()->where('is_approved', 1)->with(['user' => function ($query) {
            $query->withTrashed();
        }])->get();

        foreach ($reviews as $review) {
            $review->likesCount = Likes::where('review_id', $review->id)->where('like', 1)->count();
        }

        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
        ]);
    }

    public function favorites($gameId, Request $request)
    {
        $likesCount = Favorites::where('game_id', $gameId)->count();

        return response()->json(['likes_count' => $likesCount]);
    }

    public function actors(Request $request, $game_id)
    {
        $actors_ids = ActorGame::where('game_id', $game_id)->pluck('actor_id');
        $actors = Actor::whereIn('id', $actors_ids)->get();

        return response()->json([
            'actors' => ActorsResource::collection($actors),
        ]);
    }
}