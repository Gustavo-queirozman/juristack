<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Assinatura do contrato</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <p>Ola, <?php echo e($customer->name); ?>.</p>

    <p>
        Seu contrato de prestacao de servicos advocaticios foi emitido e precisa da sua assinatura.
        O instrumento foi preparado para formalizacao entre voce e <?php echo e(mb_strtolower($signer['counterparty_label'])); ?> <?php echo e($signer['name']); ?>.
    </p>

    <p>
        Revise o documento, assine o contrato e devolva ao escritorio pelos canais informados no atendimento.
        O arquivo tambem segue anexado neste e-mail.
    </p>

    <p style="margin: 24px 0;">
        <a href="<?php echo e($document->document_link); ?>" style="display: inline-block; background: #4f46e5; color: #ffffff; text-decoration: none; padding: 12px 18px; border-radius: 6px; font-weight: 600;">
            Baixar contrato para assinar
        </a>
    </p>

    <p>Se precisar de ajustes ou tiver alguma duvida antes de assinar, responda a este e-mail ou entre em contato com o escritorio.</p>

    <p>Atenciosamente,<br>Juristack</p>
</body>
</html>
<?php /**PATH C:\Users\TECNOLOGIA\OneDrive - Faculdade Atenas\Área de Trabalho\juristack\resources\views/emails/service-contract-signature.blade.php ENDPATH**/ ?>