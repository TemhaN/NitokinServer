<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryGameRequest;
use App\Models\Category;
use App\Models\CategoryGame;
use App\Models\Game;
use Illuminate\Http\Request;

class CategoryGameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categriesgame = CategoryGame::with('category')->get();
        $games = Game::all();
        $categories = Category::all();

        return view('admins.categorygame.index', compact('categriesgame', 'games', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $games = Game::all();

        return view('admins.categorygame.create', compact('games', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryGameRequest $request)
    {
        CategoryGame::create($request->validated());

        return redirect(route('categorygames.index'));
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
        $categorygame = CategoryGame::findOrFail($id);
        $categorygame->game_id = $request->game_id;
        $categorygame->category_id = $request->category_id;
        $categorygame->save();

        return back()->with('success', 'CategoryGame updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryGame $categorygame)
    {
        $categorygame->delete();

        return back();
    }
}