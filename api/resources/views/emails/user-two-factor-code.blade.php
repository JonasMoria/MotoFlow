
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Código de autenticação | {{ config('app.name') }}</title>
</head>

<body style="margin: 0; padding: 0; background-color: #F1F5F9; font-family: Arial, Helvetica, sans-serif; color: #1F2937;">

    <!-- Preheader: resumo exibido na caixa de entrada -->
    <div style="display: none; max-height: 0; overflow: hidden; opacity: 0; color: transparent;">
        Seu código de segurança do {{ config('app.name') }}. Utilize-o para concluir seu acesso.
    </div>

    <table
        role="presentation"
        width="100%"
        cellspacing="0"
        cellpadding="0"
        border="0"
        style="width: 100%; background-color: #F1F5F9;"
    >
        <tr>
            <td align="center" style="padding: 32px 12px;">

                <!-- Container principal -->
                <table
                    role="presentation"
                    width="600"
                    cellspacing="0"
                    cellpadding="0"
                    border="0"
                    style="width: 100%; max-width: 600px; background-color: #FFFFFF; border-radius: 12px; overflow: hidden;"
                >
                    <!-- Cabeçalho -->
                    <tr>
                        <td style="padding: 28px 32px; border-bottom: 1px solid #E2E8F0;">
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                            >
                                <tr>
                                    <td align="left" valign="middle">
                                        <img
                                            src="{{ rtrim(config('app.front_url'), '/') . '/images/motoflow-logo.png' }}"
                                            alt="{{ config('app.name')  }}"
                                            width="180"
                                            style="display: block; width: 180px; max-width: 100%; height: auto; border: 0;"
                                        >
                                    </td>

                                    <td
                                        align="right"
                                        valign="middle"
                                        style="font-size: 12px; line-height: 18px; color: #6B7280;"
                                    >
                                        Gestão inteligente<br>
                                        para sua oficina
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Conteúdo -->
                    <tr>
                        <td style="padding: 36px 32px 32px;">

                            <!-- Indicador de segurança -->
                            <table
                                role="presentation"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                                style="margin-bottom: 24px;"
                            >
                                <tr>
                                    <td
                                        align="center"
                                        style="width: 48px; height: 48px; background-color: #DBEAFE; border-radius: 12px; color: #0F3B82; font-size: 22px; font-weight: bold;"
                                    >
                                        &#128274;
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin: 0 0 16px; font-size: 26px; line-height: 34px; font-weight: 700; color: #1F2937;">
                                Seu código de autenticação
                            </h1>

                            <p style="margin: 0 0 12px; font-size: 16px; line-height: 25px; color: #1F2937;">
                                Olá, <strong>{{ $user->name }}</strong>!
                            </p>

                            <p style="margin: 0 0 28px; font-size: 15px; line-height: 25px; color: #6B7280;">
                                Recebemos uma solicitação para acessar sua conta no {{ config('app.name')  }}.
                                Utilize o código abaixo para concluir a autenticação.
                            </p>

                            <!-- Código de autenticação -->
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                                style="width: 100%; margin-bottom: 24px;"
                            >
                                <tr>
                                    <td
                                        align="center"
                                        style="padding: 24px 12px; background-color: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px;"
                                    >
                                        <p style="margin: 0 0 8px; font-size: 11px; line-height: 16px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #0F3B82;">
                                            Código de segurança
                                        </p>

                                        <p style="margin: 0; font-size: 36px; line-height: 46px; font-weight: 700; letter-spacing: 10px; color: #0F3B82; font-family: Arial, Helvetica, sans-serif;">
                                            {{ $code }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Validade -->
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                                style="margin-bottom: 24px;"
                            >
                                <tr>
                                    <td valign="top" style="width: 36px; padding-top: 2px; color: #FF8A00; font-size: 20px;">
                                        &#9201;
                                    </td>

                                    <td style="font-size: 14px; line-height: 22px; color: #6B7280;">
                                        <strong style="color: #1F2937;">Código com validade limitada</strong><br>
                                        Este código expira em alguns minutos. Utilize-o o quanto antes.
                                    </td>
                                </tr>
                            </table>

                            <!-- Aviso de segurança -->
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                                style="width: 100%; background-color: #FFF7ED; border-left: 4px solid #FF8A00; border-radius: 4px;"
                            >
                                <tr>
                                    <td style="padding: 16px;">
                                        <p style="margin: 0 0 6px; font-size: 14px; line-height: 21px; font-weight: 700; color: #92400E;">
                                            Você não solicitou este código?
                                        </p>

                                        <p style="margin: 0; font-size: 13px; line-height: 21px; color: #92400E;">
                                            Ignore este e-mail e não compartilhe o código com ninguém.
                                            Nossa equipe nunca solicitará seu código de autenticação.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 28px 0 0; font-size: 14px; line-height: 23px; color: #6B7280;">
                                Atenciosamente,<br>
                                <strong style="color: #1F2937;">Equipe {{ config('app.name')  }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Rodapé -->
                    <tr>
                        <td
                            align="center"
                            style="padding: 24px 32px; background-color: #1F2937;"
                        >
                            <p style="margin: 0 0 8px; font-size: 14px; line-height: 21px; font-weight: 700; color: #FFFFFF;">
                                Moto<span style="color: #FF8A00;">Flow</span>
                            </p>

                            <p style="margin: 0; font-size: 12px; line-height: 19px; color: #CBD5E1;">
                                Tecnologia que move sua oficina.
                            </p>

                            <p style="margin: 12px 0 0; font-size: 11px; line-height: 17px; color: #9CA3AF;">
                                E-mail automático de segurança. Não é necessário responder.
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="margin: 20px 0 0; font-size: 11px; line-height: 18px; text-align: center; color: #9CA3AF;">
                    © {{ date('Y') }} {{ config('app.name')  }}. Todos os direitos reservados.
                </p>

            </td>
        </tr>
    </table>
</body>
</html>
