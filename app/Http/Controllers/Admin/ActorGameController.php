<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActorGameRequest;
use App\Models\Actor;
use App\Models\ActorGame;
use App\Models\Game;
use Illuminate\Http\Request;

class ActorGameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $actorsgame = ActorGame::with('actor')->get();
        $games = Game::all();
        $actors = Actor::all();

        return view('admins.actorgame.index', compact('actorsgame', 'games', 'actors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $actors = Actor::all();
        $games = Game::all();

        return view('admins.actorgame.create', compact('games', 'actors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActorGameRequest $request)
    {
        ActorGame::create($request->validated());

        return redirect(route('actorgames.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $actorgame = ActorGame::findOrFail($id);
        $actorgame->game_id = $request->game_id;
        $actorgame->actor_id = $request->actor_id;
        $actorgame->save();

        return back()->with('success', 'Actorgame updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActorGame $actorgame)
    {
        $actorgame->delete();

        return back();
    }
}