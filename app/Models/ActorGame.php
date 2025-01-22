<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActorGame extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    protected $fillable = [
        'actor_id',
        'game_id',
    ];
}