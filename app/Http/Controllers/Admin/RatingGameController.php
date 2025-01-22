<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;

class RatingGameController extends Controller
{
    public function index()
    {
        $ratings = Rating::all();

        return view('admins.ratings.index', compact('ratings'));
    }

    // Вывод оценок только выбранного фильма
    public function show($game_id)
    {
        $averageRating = Rating::where('game_id', $game_id)->avg('ball');
        $ratings = Rating::where('game_id', $game_id)->get();

        return view('ratings.show', compact('ratings', 'averageRating'));
    }

    // Удаление оценки фильма
    public function destroy($id)
    {
        $rating = Rating::find($id);
        $rating->delete();

        return redirect()->route('ratings.index')->with('success', 'Оценка удалена успешно');
    }
}