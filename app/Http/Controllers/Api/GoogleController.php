<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Google\Client;

class GoogleController extends Controller
{
    public function connect()
    {
        // Настройка клиента Google
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessType('offline');
        $client->setScopes(['https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email']);

        // URL для авторизации
        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        // Настройка клиента Google
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        // Получение кода авторизации
        $code = $request->input('code');

        if ($code) {
            // Обмен кода на токен доступа
            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (!isset($token['error'])) {
                $client->setAccessToken($token['access_token']);

                // Получение информации о пользователе
                $oauth2 = new \Google\Service\Oauth2($client);
                $googleUserInfo = $oauth2->userinfo->get();

                $email = $googleUserInfo->email;
                $username = $googleUserInfo->name ?? explode('@', $email)[0]; // Имя пользователя или часть email

                // Проверка, существует ли пользователь
                $user = User::where('email', $email)->first();

                if (!$user) {
                    // Если пользователь не найден, создаем его
                    $user = User::create([
                        'username' => $username,
                        'email' => $email,
                        'password' => Hash::make(uniqid()), // Генерация случайного пароля
                        'email_verified_at' => now(), // Автоматическая верификация
                    ]);
                } else {
                    // Если пользователь найден, сразу подтверждаем почту
                    $user->email_verified_at = now();
                    $user->save();
                }

                // Авторизация пользователя
                auth()->login($user);

                // Генерация токена
                $token = $user->createToken($email);

                return response([
                    'status' => 'success',
                    'token' => $token->plainTextToken,
                    'id' => $user->id,
                    'username' => $user->username,
                ]);
            }
    }

        return response([
            'status' => 'error',
            'message' => 'Unable to authenticate with Google.',
        ], 401);
    }

}