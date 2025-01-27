<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    // Указываем таблицу, с которой работает эта модель
    protected $table = 'password_resets';

    // Разрешаем массовое назначение этих полей
    protected $fillable = [
        'email',
        'recovery_code',
        'expires_at',
    ];

    // Указываем, что поля 'created_at' и 'updated_at' не используются
    public $timestamps = false;
}