<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Game;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function show($gameId, Request $request)
    {
        $game = Game::find($gameId);

        if (! $game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        $reviews = $game->reviews()->where('is_approved', true)->get();

        if ($request->has('search')) {
            $searchTerms = explode('%', $request->query('search'));
            foreach ($searchTerms as $searchTerm) {
                $reviews = $reviews->where(function ($query) use ($searchTerm) {
                    $query->where('message', 'like', '%'.$searchTerm.'%')
                        ->orWhereHas('user', function ($query) use ($searchTerm) {
                            $query->where('username', 'like', '%'.$searchTerm.'%');
                        });
                });
            }
        }

        return response()->json([
            'reviews' => ReviewResource::collection($reviews),
        ], 200);
    }
}