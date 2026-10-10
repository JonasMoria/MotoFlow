<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço</title>
</head>
<body>
    <p>
        Olá, {{ $repairOrder['client']['name'] ?? 'cliente' }}!
    </p>

    <p>
        Segue em anexo o PDF da Ordem de Serviço
        <strong>#{{ $repairOrder['id'] }}</strong>.
    </p>

    <p>
        <strong>Motocicleta:</strong>
        {{ $repairOrder['motorcycle']['brand'] ?? '' }}
        {{ $repairOrder['motorcycle']['model'] ?? '' }}
    </p>

    <p>
        <strong>Placa:</strong>
        {{ $repairOrder['motorcycle']['plate'] ?? 'Não informada' }}
    </p>

    <p>
        Consulte o documento anexado para conferir os detalhes,
        peças, serviços e valores da ordem de serviço.
    </p>

    <p>
        Atenciosamente,<br>
        Equipe MotoFlow
    </p>
</body>
</html>
