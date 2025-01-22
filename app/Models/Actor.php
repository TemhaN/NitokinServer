<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'img_link',
    ];

    public function actors()
    {
        return $this->hasMany(Actor::class);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'actor_games');
    }
}