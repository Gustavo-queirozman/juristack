<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Contrato para assinatura</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <p>Olá, {{ $customer->name }}.</p>

    <p>
        Segue em anexo o contrato de prestação de serviços advocatícios para assinatura.
        O instrumento foi preparado para formalização entre você e {{ mb_strtolower($signer['counterparty_label']) }} {{ $signer['name'] }}.
    </p>

    <p>
        Após a conferência, utilize o arquivo anexo para assinar e devolver ao escritório pelos canais informados no atendimento.
    </p>

    <p>
        Documento: <a href="{{ $document->document_link }}">baixar contrato</a>
    </p>

    <p>Atenciosamente,<br>Juristack</p>
</body>
</html>
