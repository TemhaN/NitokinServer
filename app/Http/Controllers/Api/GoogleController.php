<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Google\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GoogleController
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
                $name = $googleUserInfo->name;

                // Проверка, существует ли пользователь
                $user = User::where('email', $email)->first();

                if (!$user) {
                    // Создание нового пользователя
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make(uniqid()), // Генерация случайного пароля
                    ]);
                }

                // Аутентификация пользователя
                auth()->login($user);

                // Генерация токена
                $token = $user->createToken($email)->plainTextToken;

                return response([
                    'status' => 'success',
                    'token' => $token,
                    'id' => $user->id,
                    'name' => $user->name,
                ]);
            }
        }

        return response([
            'status' => 'error',
            'message' => 'Unable to authenticate with Google.',
        ], 401);
    }
}