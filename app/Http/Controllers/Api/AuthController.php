<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log; // Добавьте этот use в начало файла
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        Log::info('Регистрация пользователя', ['data' => $data]);

        // Создаем пользователя
        $user = User::create($data);

        Log::info('Пользователь создан', ['user' => $user]);

        // Выполняем автоматический вход
        if (auth()->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            Log::info('Пользователь успешно вошел', ['email' => $data['email']]);
        } else {
            Log::warning('Ошибка при попытке входа пользователя', ['email' => $data['email']]);
        }

        // Генерируем токен
        $token = auth()->user()->createToken($data['email']);
        Log::info('Токен сгенерирован', ['token' => $token->plainTextToken]);

        // Отправляем письмо для подтверждения email
        $this->sendVerificationEmail($user);

        return response([
            'status' => 'success',
            'token' => $token->plainTextToken,
            'id' => $user->id,
            'username' => $user->username,
            'message' => 'Проверьте вашу почту для подтверждения email.',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        Log::info('Попытка входа', ['credentials' => $credentials]);

        if (!auth()->attempt($credentials)) {
            Log::warning('Неверные данные для входа', ['credentials' => $credentials]);
            return response([
                'status' => 'invalid',
                'message' => 'Wrong email or password',
            ], 401);
        }

        $token = auth()->user()->createToken($credentials['email']);
        Log::info('Токен сгенерирован для входа', ['token' => $token->plainTextToken]);

        return response([
            'status' => 'success',
            'token' => $token->plainTextToken,
            'id' => auth()->user()->id,
            'username' => auth()->user()->username,
        ]);
    }

    public function signout(Request $request)
    {
        Log::info('Попытка выхода пользователя', ['user_id' => $request->user()->id]);

        $request->user()->currentAccessToken()->delete();

        return response(['status' => 'success']);
    }

    public function sendVerificationEmail(User $user)
    {
        try {
            Log::info('Отправка письма для подтверждения', ['user_email' => $user->email]);

            // Формируем URL для подтверждения почты
            $verificationUrl = url("/api/v1/verify-email?email={$user->email}&token=" . base64_encode($user->id));
            Log::info('Сформирован URL для подтверждения', ['verification_url' => $verificationUrl]);

            // Отправляем email с использованием шаблона
            Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl));
            Log::info('Письмо отправлено для подтверждения', ['user_email' => $user->email]);

            return response([
                'status' => 'success',
                'message' => 'Письмо для подтверждения отправлено.',
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при отправке письма для подтверждения: ' . $e->getMessage());
            return response([
                'status' => 'error',
                'message' => 'Не удалось отправить email: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        try {
            Log::info('Попытка подтверждения email', ['request' => $request->all()]);

            // Получаем email и токен из URL
            $email = $request->query('email');
            $token = $request->query('token');

            // Декодируем токен
            $userId = base64_decode($token);
            Log::info('Токен декодирован', ['user_id' => $userId]);

            // Находим пользователя по ID
            $user = User::findOrFail($userId);
            Log::info('Пользователь найден', ['user' => $user]);

            // Проверяем, соответствует ли email
            if ($user->email !== $email) {
                Log::warning('Email не совпадает', ['user_email' => $user->email, 'provided_email' => $email]);
                return response([
                    'status' => 'error',
                    'message' => 'Email адрес не совпадает.',
                ], 400);
            }

            // Подтверждаем email
            $user->email_verified_at = now();
            $user->save();
            Log::info('Email подтвержден', ['user_id' => $user->id]);

            // Отправляем успешный ответ
            return response([
                'status' => 'success',
                'message' => 'Ваш email был успешно подтвержден.',
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при подтверждении email: ' . $e->getMessage());
            return response([
                'status' => 'error',
                'message' => 'Не удалось подтвердить email: ' . $e->getMessage(),
            ], 500);
        }
    }
}