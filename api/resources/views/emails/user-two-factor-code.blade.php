<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Código de autenticação</title>
</head>
<body>
    <h1>Seu código de autenticação</h1>

    <p>Olá, {{ $user->name }}!</p>

    <p>Seu código de autenticação é:</p>

    <h2>{{ $code }}</h2>

    <p>Esse código possui validade limitada.</p>

    <p>
        Se você não solicitou esse código, ignore este e-mail.
    </p>
</body>
</html>