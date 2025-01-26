<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Подтверждение почты</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f7fc;
      margin: 0;
      padding: 0;
      color: #333;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
      color: #8441A3;
      text-align: center;
    }

    p {
      font-size: 16px;
      line-height: 1.6;
    }

    .btn {
      display: block;
      background-color: #8441A3;
      color: white;
      padding: 12px;
      margin: 50px auto 120px auto;
      text-align: center;
      text-decoration: none;
      border-radius: 50px;
      font-weight: bold;
      font-size: 16px;
      width: 200px;
    }

    .btn:hover {
      background-color: #703595;
    }

    .footer {
      text-align: center;
      margin-top: 30px;
      font-size: 14px;
      color: #777;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Здравствуйте, {{ $user->username }}!</h2>
    <p>Пожалуйста, подтвердите свою почту, перейдя по следующей ссылке:</p>
    <a href="{{ $verificationUrl }}" class="btn">Подтвердить почту</a>
    <div class="footer">
      <p>Если вы не регистрировались на нашем сайте, просто проигнорируйте это письмо.</p>
    </div>
  </div>
</body>

</html>
