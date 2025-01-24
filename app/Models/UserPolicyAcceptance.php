<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPolicyAcceptance extends Model
{
    // Указываем таблицу, если она отличается от имени модели во множественном числе
    protected $table = 'user_policy_acceptances';

    protected $fillable = [
        'user_id', 'policy_id', 'accepted_at'
    ];

    // Связь с пользователем (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с политикой (Policy)
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }
}