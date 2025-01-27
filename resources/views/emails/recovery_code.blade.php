<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <title>Код восстановления</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      text-align: center;
      padding: 30px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h3 {
      color: #333;
    }

    .code {
      font-size: 48px;
      font-weight: bold;
      color: #8441A3;
      margin-top: 20px;
      padding: 20px;
      background-color: #f1e6f7;
      border-radius: 5px;
    }

    p {
      font-size: 16px;
      color: #555;
      margin: 10px 0;
    }

    .timeout-text {
      margin-top: 20px;
      font-size: 16px;
      color: #555;
    }

    .footer {
      font-size: 14px;
      color: #777;
      margin-top: 30px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h3>Здравствуйте, {{ $user->username }}!</h3>
    <p>Ваш код восстановления доступа:</p>
    <div class="code">{{ $recoveryCode }}</div>
    <p class="timeout-text">Этот код действует всего 10 минут.</p>
    <div class="footer">
      <p>Если это не вы выслали запрос на восстановление пароля, просто проигнорируйте это письмо.</p>
    </div>
  </div>
</body>

</html>
