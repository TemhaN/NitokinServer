<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameRequest;
use App\Models\Category;
use App\Models\Country;
use App\Models\Game;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::all();
        $categories = Category::all();

        return view('admins.games.index', compact('games'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Game $game)
    {

        $game = Game::all();

        $countries = Country::all();
        $categories = Category::all();

        return view('admins.games.create', compact('countries', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GameRequest $request)
    {
        $data = $request->validated();

        Game::create($data);

        return redirect(route('games.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        $games = Game::all();
        $countries = Country::all();
        $categories = Category::all();

        return view('admins.games.create', compact('game', 'countries', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GameRequest $request, string $id)
    {
        $game = Game::findOrFail($id);
        $game->update($request->validated());

        return redirect(route('games.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        $game->delete();

        return redirect(route('games.index'));
    }
}