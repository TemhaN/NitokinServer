<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use App\Models\Category;
use App\Models\CategoryGame;
use App\Models\Country;
use App\Models\Game;
use App\Models\Rating;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $gamesCount = Game::count();
        $categoriesCount = Category::count();
        $countriesCount = Country::count();
        $ratingsCount = Rating::count();
        $actorsCount = Actor::count();

        $categoryGameCounts = CategoryGame::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        $categoryNames = Category::whereIn('id', $categoryGameCounts->pluck('category_id'))->pluck('name', 'id')->values()->all();

        $averageRatings = Rating::select('game_id', DB::raw('AVG(ball) as average_rating'))
            ->groupBy('game_id')
            ->get();

        $topRatedGames = $averageRatings->sortByDesc('average_rating')->take(5);

        $gameNames = Game::whereIn('id', $topRatedGames->pluck('game_id'))->pluck('name', 'id')->values()->all();

        $reviews = Review::where('is_approved', 0)->get();
        $games = Game::withCount('reviews')->get();
        $ratings = Rating::all();

        $data = [
            'usersCount' => $usersCount,
            'gamesCount' => $gamesCount,
            'topRatedGames' => $topRatedGames,
            'gameNames' => $gameNames,
            'categoriesCount' => $categoriesCount,
            'countriesCount' => $countriesCount,
            'ratingsCount' => $ratingsCount,
            'actorsCount' => $actorsCount,
            'categoryGameCounts' => $categoryGameCounts,
            'categoryNames' => $categoryNames,
            'reviews' => $reviews,
            'games' => $games,
            'ratings' => $ratings,
        ];

        // $game = game::findOrFail($game_id);

        return view('index', $data);
    }
}