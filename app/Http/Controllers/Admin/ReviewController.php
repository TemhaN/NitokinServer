<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Rating;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::all();
        $games = Game::withCount('reviews')->get();

        // $game = game::findOrFail($game_id);
        $ratings = Rating::all();

        return view('admins.reviews.index', compact('reviews', 'games', 'ratings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function approved($id)
    {
        $review = Review::findOrFail($id);
        $review->is_approved = true;
        $review->save();

        return redirect()->back()->with('success', 'Отзыв одобрен!');
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
    public function show($game_id)
    {
        $users = User::all();
        $reviews = Review::where('game_id', $game_id)->get();
        $game = Game::findOrFail($game_id);

        $averageRating = round(Rating::where('game_id', $game_id)->avg('ball'), 1);
        $ratings = Rating::where('game_id', $game_id)->with('user')->get();

        return view('admins.games.show', compact('reviews', 'game', 'users', 'ratings', 'averageRating'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function approve($id)
    {
        $review = Review::find($id);
        $review->is_approved = true;
        $review->save();

        return back();
    }

    public function toggle($id)
    {
        $review = Review::find($id);
        $review->is_approved = ! $review->is_approved;
        $review->save();

        return back();
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
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Отзыв удален!');
    }
}