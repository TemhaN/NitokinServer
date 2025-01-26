<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
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

        // Создаем пользователя
        $user = User::create($data);

        // Выполняем автоматический вход
        auth()->attempt(['email' => $data['email'], 'password' => $data['password']]);

        // Генерируем токен
        $token = auth()->user()->createToken($data['email']);

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

        if (!auth()->attempt($credentials)) {
            return response([
                'status' => 'invalid',
                'message' => 'Wrong email or password',
            ], 401);
        }

        $token = auth()->user()->createToken($credentials['email']);

        return response([
            'status' => 'success',
            'token' => $token->plainTextToken,
            'id' => auth()->user()->id,
            'username' => auth()->user()->username,
        ]);
    }

    public function signout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response(['status' => 'success']);
    }

    private function sendVerificationEmail(User $user)
    {
        $client = new Client();
        $client->setApplicationName(env('GOOGLE_PROJECT_ID')); // Название проекта из .env
        $client->setScopes(Gmail::GMAIL_SEND); // Доступ к отправке Gmail
        $client->setAuthConfig([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uris' => [env('GOOGLE_REDIRECT_URI')],
            'auth_uri' => env('GOOGLE_AUTH_URI'),
            'token_uri' => env('GOOGLE_TOKEN_URI'),
            'auth_provider_x509_cert_url' => env('GOOGLE_AUTH_PROVIDER_CERT_URL'),
        ]);
        $client->setAccessType('offline'); // Чтобы получить обновляемый токен


        $service = new Gmail($client);

        // Формируем сообщение
        $email = "To: {$user->email}\r\n";
        $email .= "Subject: Подтверждение почты\r\n";
        $email .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $email .= "Здравствуйте, {$user->username}!<br><br>";
        $email .= "Пожалуйста, подтвердите вашу почту, перейдя по ссылке ниже:<br>";
        $email .= "<a href='" . url("/api/verify-email?email={$user->email}&token=" . base64_encode($user->id)) . "'>Подтвердить почту</a>";

        $rawMessage = base64_encode($email);
        $rawMessage = str_replace(['+', '/', '='], ['-', '_', ''], $rawMessage);

        $message = new Message();
        $message->setRaw($rawMessage);

        // Отправляем письмо
        try {
            $service->users_messages->send('me', $message);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => 'Не удалось отправить email: ' . $e->getMessage(),
            ], 500);
        }
    }
}