<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">

    <title>Ordem de Serviço - {{ $repairOrder['id'] }}</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
            line-height: 1.4;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header table,
        .info-table,
        .items-table,
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
        }

        .header-subtitle {
            color: #666;
            font-size: 9px;
            margin-top: 4px;
        }

        .order-number {
            text-align: right;
            font-size: 13px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            background: #eee;
            padding: 7px;
            margin-bottom: 8px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .items-table th {
            background: #eee;
            font-weight: bold;
            text-align: left;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ccc;
            padding: 7px 6px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-table {
            margin-top: 10px;
            width: 55%;
            margin-left: auto;
        }

        .totals-table td {
            padding: 5px;
            text-align: right;
        }

        .grand-total {
            font-size: 13px;
            font-weight: bold;
            border-top: 2px solid #222;
        }

        .description {
            border: 1px solid #ddd;
            padding: 8px;
            min-height: 25px;
            white-space: pre-line;
        }

        .empty {
            color: #777;
            text-align: center;
            padding: 12px;
        }

        .signatures {
            margin-top: 55px;
            width: 100%;
        }

        .signature {
            width: 45%;
            text-align: center;
            border-top: 1px solid #555;
            padding-top: 7px;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 8px;
            color: #777;
        }
    </style>
</head>

<body>
    @php
        $client = $repairOrder['client'] ?? [];
        $motorcycle = $repairOrder['motorcycle'] ?? [];
        $parts = $repairOrder['parts'] ?? [];
        $services = $repairOrder['services'] ?? [];

        $formatMoney = static fn ($value): string =>
            'R$ ' . number_format((float) ($value ?? 0), 2, ',', '.');

        $formatDate = static function ($value): string {
            if (empty($value)) {
                return 'Não informado';
            }

            try {
                return \Carbon\Carbon::parse($value)->format('d/m/Y H:i');
            } catch (\Throwable $exception) {
                return (string) $value;
            }
        };

        $partsTotal = collect($parts)->sum(
            static fn ($part) =>
                (float) ($part['repair_part_quantity'] ?? 0)
                * (float) ($part['repair_part_unit_value'] ?? 0)
        );

        $servicesTotal = collect($services)->sum(
            static fn ($service) =>
                (float) ($service['repair_service_quantity'] ?? 0)
                * (float) ($service['repair_service_unit_value'] ?? 0)
        );

        $grandTotal = $partsTotal + $servicesTotal;
    @endphp

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="header-title">ORDEM DE SERVIÇO</div>
                    <div class="header-subtitle">
                        Relatório de atendimento e manutenção de motocicleta
                    </div>
                </td>
                <td class="order-number">
                    OS #{{ $repairOrder['id'] ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Dados da ordem de serviço --}}
    <div class="section">
        <div class="section-title">Dados da Ordem de Serviço</div>

        <table class="info-table">
            <tr>
                <td width="20%">
                    <span class="label">Título:</span>
                </td>
                <td width="80%">
                    {{ $repairOrder['title'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Status:</span>
                </td>
                <td>
                    {{ $repairOrder['status'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Urgência:</span>
                </td>
                <td>
                    {{ $repairOrder['urgency_level'] ?? 'Não informada' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Criada em:</span>
                </td>
                <td>
                    {{ $formatDate($repairOrder['created_at'] ?? null) }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Iniciada em:</span>
                </td>
                <td>
                    {{ $formatDate($repairOrder['started_at'] ?? null) }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Finalizada em:</span>
                </td>
                <td>
                    {{ $formatDate($repairOrder['finished_at'] ?? null) }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Entregue em:</span>
                </td>
                <td>
                    {{ $formatDate($repairOrder['delivered_at'] ?? null) }}
                </td>
            </tr>
        </table>

        <p class="label" style="margin: 8px 0 5px;">Descrição</p>
        <div class="description">
            {{ $repairOrder['description'] ?? 'Nenhuma descrição informada.' }}
        </div>

        <p class="label" style="margin: 8px 0 5px;">Diagnóstico</p>
        <div class="description">
            {{ $repairOrder['diagnosis'] ?? 'Nenhum diagnóstico informado.' }}
        </div>

        <p class="label" style="margin: 8px 0 5px;">Observações</p>
        <div class="description">
            {{ $repairOrder['observations'] ?? 'Nenhuma observação informada.' }}
        </div>
    </div>

    {{-- Dados do cliente --}}
    <div class="section">
        <div class="section-title">Dados do Cliente</div>

        <table class="info-table">
            <tr>
                <td width="20%">
                    <span class="label">Nome:</span>
                </td>
                <td width="80%">
                    {{ $client['name'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Telefone:</span>
                </td>
                <td>
                    {{ $client['phone'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">E-mail:</span>
                </td>
                <td>
                    {{ $client['email'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Documento:</span>
                </td>
                <td>
                    {{ $client['document'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Endereço:</span>
                </td>
                <td>
                    {{ $client['address'] ?? 'Não informado' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Dados da motocicleta --}}
    <div class="section">
        <div class="section-title">Dados da Motocicleta</div>

        <table class="info-table">
            <tr>
                <td width="20%">
                    <span class="label">Marca:</span>
                </td>
                <td width="30%">
                    {{ $motorcycle['brand'] ?? 'Não informado' }}
                </td>

                <td width="20%">
                    <span class="label">Modelo:</span>
                </td>
                <td width="30%">
                    {{ $motorcycle['model'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Placa:</span>
                </td>
                <td>
                    {{ $motorcycle['plate'] ?? 'Não informado' }}
                </td>

                <td>
                    <span class="label">Ano:</span>
                </td>
                <td>
                    {{ $motorcycle['year'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Cor:</span>
                </td>
                <td>
                    {{ $motorcycle['color'] ?? 'Não informada' }}
                </td>

                <td>
                    <span class="label">Renavam:</span>
                </td>
                <td>
                    {{ $motorcycle['renavam'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Chassi:</span>
                </td>
                <td colspan="3">
                    {{ $motorcycle['chassis'] ?? 'Não informado' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Nº do motor:</span>
                </td>
                <td colspan="3">
                    {{ $motorcycle['engine_number'] ?? 'Não informado' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Peças --}}
    <div class="section">
        <div class="section-title">Peças Utilizadas</div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th class="text-center" width="12%">Qtd.</th>
                    <th class="text-right" width="20%">Valor unitário</th>
                    <th class="text-right" width="20%">Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($parts as $part)
                    @php
                        $quantity = (float) ($part['repair_part_quantity'] ?? 0);
                        $unitValue = (float) ($part['repair_part_unit_value'] ?? 0);
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ $part['repair_part_name'] ?? 'Peça' }}
                            </strong>

                            @if (!empty($part['repair_part_description']))
                                <br>
                                {{ $part['repair_part_description'] }}
                            @endif
                        </td>

                        <td class="text-center">
                            {{ number_format($quantity, 2, ',', '.') }}
                        </td>

                        <td class="text-right">
                            {{ $formatMoney($unitValue) }}
                        </td>

                        <td class="text-right">
                            {{ $formatMoney($quantity * $unitValue) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">
                            Nenhuma peça registrada nesta ordem de serviço.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Serviços --}}
    <div class="section">
        <div class="section-title">Serviços Executados</div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th class="text-center" width="12%">Qtd.</th>
                    <th class="text-right" width="20%">Valor unitário</th>
                    <th class="text-right" width="20%">Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($services as $service)
                    @php
                        $quantity = (float) ($service['repair_service_quantity'] ?? 0);
                        $unitValue = (float) ($service['repair_service_unit_value'] ?? 0);
                    @endphp

                    <tr>
                        <td>
                            <strong>
                                {{ $service['repair_service_name'] ?? 'Serviço' }}
                            </strong>

                            @if (!empty($service['repair_service_description']))
                                <br>
                                {{ $service['repair_service_description'] }}
                            @endif
                        </td>

                        <td class="text-center">
                            {{ number_format($quantity, 2, ',', '.') }}
                        </td>

                        <td class="text-right">
                            {{ $formatMoney($unitValue) }}
                        </td>

                        <td class="text-right">
                            {{ $formatMoney($quantity * $unitValue) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">
                            Nenhum serviço registrado nesta ordem de serviço.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Subtotal de peças:</td>
                <td>{{ $formatMoney($partsTotal) }}</td>
            </tr>

            <tr>
                <td>Subtotal de serviços:</td>
                <td>{{ $formatMoney($servicesTotal) }}</td>
            </tr>

            <tr class="grand-total">
                <td>Total geral:</td>
                <td>{{ $formatMoney($grandTotal) }}</td>
            </tr>
        </table>
    </div>

    {{-- Assinaturas --}}
    <table class="signatures">
        <tr>
            <td width="50%" style="padding-right: 20px;">
                <div class="signature">
                    Assinatura do responsável pela oficina
                </div>
            </td>

            <td width="50%" style="padding-left: 20px;">
                <div class="signature">
                    Assinatura do cliente
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i') }}.
        Ordem de Serviço #{{ $repairOrder['id'] ?? '-' }}.
    </div>
</body>
</html>