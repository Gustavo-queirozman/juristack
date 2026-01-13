<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JurisTech | Tecnologia Jurídica de Ponta</title>
    <meta name="description" content="Transforme sua prática jurídica com inteligência artificial. Gestão de processos, análise de documentos e automação para advogados modernos.">
    <meta name="generator" content="v0.app">
    
    <!-- Icons -->
    <link rel="icon" href="{{ asset('icon-light-32x32.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" href="{{ asset('icon-dark-32x32.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-icon.png') }}">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/globals.css') }}">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased">
    @yield('content')
    
    <!-- Scripts -->
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>

