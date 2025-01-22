<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Rating;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getTopRatedGameLink()
    {
        $topRatedGames = Rating::orderBy('ball', 'desc')->take(10)->pluck('game_id');

        $randomGameId = $topRatedGames->random();

        $game = Game::find($randomGameId);
        $linkVideo = $game->link_video;

        return response()->json(['link_video' => $linkVideo]);
    }

    public function getTopRatedGameList()
    {
        $topRatedGamesIds = Rating::orderBy('ball', 'desc')->take(10)->pluck('game_id');

        $topRatedGames = Game::whereIn('id', $topRatedGamesIds)->get();

        return response()->json(['games' => $topRatedGames]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}