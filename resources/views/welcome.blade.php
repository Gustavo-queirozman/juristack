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
    
    <!-- Styles e scripts (build Vite: app.css inclui tema; main.js = scroll/nav) -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/main.js'])
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased">
    <main class="min-h-screen bg-background">
        
        @php
        $features = [
            [
                'icon' => 'clock',
                'title' => 'Acompanhamento em Tempo Real',
                'description' => 'Monitore todos os seus processos com atualizações automáticas e notificações inteligentes.',
            ],
            [
                'icon' => 'file-search',
                'title' => 'Análise de Documentos',
                'description' => 'O JurisTech Assist analisa contratos e documentos jurídicos em segundos com precisão de IA.',
            ],
            [
                'icon' => 'file-text',
                'title' => 'Criação Automatizada',
                'description' => 'Gere documentos, contratos e petições personalizadas com nosso assistente inteligente.',
            ],
            [
                'icon' => 'dollar-sign',
                'title' => 'Gestão Financeira',
                'description' => 'Controle honorários, despesas e faturamento com ferramentas financeiras integradas.',
            ],
            [
                'icon' => 'users',
                'title' => 'Fidelização de Clientes',
                'description' => 'CRM jurídico completo para fortalecer relacionamentos e aumentar a satisfação.',
            ],
            [
                'icon' => 'bar-chart',
                'title' => 'Relatórios Inteligentes',
                'description' => 'Dashboards e análises avançadas para decisões estratégicas baseadas em dados.',
            ],
            [
                'icon' => 'shield',
                'title' => 'Segurança Jurídica',
                'description' => 'Criptografia de ponta e conformidade total com LGPD para proteção de dados sensíveis.',
            ],
            [
                'icon' => 'zap',
                'title' => 'Automação de Tarefas',
                'description' => 'Automatize tarefas repetitivas e ganhe até 40% mais tempo para focar no que importa.',
            ],
        ];
        
        $capabilities = [
            'Análise de contratos em minutos',
            'Identificação de cláusulas abusivas',
            'Sugestões de melhorias automáticas',
            'Geração de documentos personalizados',
            'Pesquisa jurisprudencial instantânea',
            'Resumos executivos de processos',
        ];
        
        $benefits = [
            [
                'icon' => 'trending-up',
                'metric' => '40%',
                'label' => 'Aumento de Produtividade',
                'description' => 'Advogados economizam em média 40% do tempo em tarefas administrativas',
            ],
            [
                'icon' => 'award',
                'metric' => '95%',
                'label' => 'Taxa de Satisfação',
                'description' => 'Clientes reportam maior satisfação com acompanhamento em tempo real',
            ],
            [
                'icon' => 'clock',
                'metric' => '10h',
                'label' => 'Economizadas por Semana',
                'description' => 'Tempo médio economizado com automação e assistente de IA',
            ],
            [
                'icon' => 'users',
                'metric' => '2.500+',
                'label' => 'Advogados Ativos',
                'description' => 'Profissionais jurídicos transformando sua prática com JurisTech',
            ],
        ];
        
        $testimonials = [
            [
                'quote' => 'O JurisTech revolucionou nosso escritório. A análise automática de contratos economiza horas do nosso time e os insights são impressionantes.',
                'author' => 'Dra. Maria Silva',
                'role' => 'Sócia - Silva & Associados',
                'image' => '/professional-woman-lawyer.png',
            ],
            [
                'quote' => 'A gestão financeira integrada e o acompanhamento de processos em tempo real tornaram nossa operação muito mais eficiente e transparente.',
                'author' => 'Dr. Carlos Mendes',
                'role' => 'Advogado Autônomo',
                'image' => '/professional-lawyer.png',
            ],
            [
                'quote' => 'O JurisTech Assist é como ter um assistente júnior trabalhando 24/7. A qualidade dos documentos gerados é excepcional.',
                'author' => 'Dra. Ana Costa',
                'role' => 'Diretora Jurídica - TechCorp',
                'image' => '/professional-woman-executive.png',
            ],
        ];
        
        $plans = [
            [
                'name' => 'Starter',
                'price' => 'R$ 297',
                'period' => '/mês',
                'description' => 'Perfeito para advogados autônomos',
                'features' => [
                    'Até 50 processos ativos',
                    '100 análises com IA por mês',
                    'Gestão financeira básica',
                    'CRM para clientes',
                    'Suporte por email',
                ],
                'cta' => 'Começar Agora',
                'popular' => false,
            ],
            [
                'name' => 'Professional',
                'price' => 'R$ 597',
                'period' => '/mês',
                'description' => 'Ideal para escritórios em crescimento',
                'features' => [
                    'Processos ilimitados',
                    'Análises ilimitadas com IA',
                    'Gestão financeira completa',
                    'CRM avançado + automações',
                    'Relatórios personalizados',
                    'Suporte prioritário',
                    'Integrações avançadas',
                ],
                'cta' => 'Começar Agora',
                'popular' => true,
            ],
            [
                'name' => 'Enterprise',
                'price' => 'Personalizado',
                'period' => '',
                'description' => 'Para grandes escritórios e departamentos',
                'features' => [
                    'Tudo do Professional',
                    'Usuários ilimitados',
                    'Treinamento dedicado',
                    'Gerente de conta',
                    'SLA garantido',
                    'Customizações personalizadas',
                    'API completa',
                ],
                'cta' => 'Falar com Vendas',
                'popular' => false,
            ],
        ];
        @endphp

        <!-- Navigation -->
        <nav id="navigation" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-transparent">
            <div class="container mx-auto px-4 md:px-6">
                <div class="flex items-center justify-between h-16 md:h-20">
                    <div class="flex items-center gap-2">
                        <svg class="h-7 w-7 md:h-8 md:w-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                        <span class="text-xl md:text-2xl font-bold text-foreground">JurisTech</span>
                    </div>

                    <div class="hidden md:flex items-center gap-8">
                        <a href="#recursos" class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                            Recursos
                        </a>
                        <a href="#assistente" class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                            Assistente
                        </a>
                        <a href="#beneficios" class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                            Benefícios
                        </a>
                        <a href="#precos" class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                            Preços
                        </a>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="hidden md:inline-flex h-8 items-center rounded-md gap-1.5 px-3 text-sm font-medium transition-all hover:bg-accent hover:text-accent-foreground">
                            Entrar
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex h-8 items-center rounded-md gap-1.5 px-3 text-sm font-medium bg-secondary text-secondary-foreground hover:bg-secondary/90 transition-all">
                            Começar Agora
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 md:pt-40 md:pb-32 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-primary/5 via-background to-background"></div>
            <div class="absolute top-20 left-10 w-72 h-72 bg-secondary/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-primary/5 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>

            <div class="container mx-auto px-4 md:px-6 relative z-10">
                <div class="max-w-4xl mx-auto text-center space-y-8">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-secondary/10 border border-secondary/20 animate-fade-in-up">
                        <svg class="h-4 w-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        <span class="text-sm font-medium text-foreground">Tecnologia Jurídica de Nova Geração</span>
                    </div>

                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-balance leading-tight animate-fade-in-up" style="animation-delay: 0.1s;">
                        Transforme sua prática jurídica com <span class="text-secondary">Inteligência Artificial</span>
                    </h1>

                    <p class="text-lg md:text-xl text-muted-foreground text-balance max-w-2xl mx-auto leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s;">
                        Automatize processos, analise documentos em segundos e gerencie seu escritório com a solução mais avançada do mercado jurídico.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                        <button class="bg-secondary text-secondary-foreground hover:bg-secondary/90 text-base px-8 h-12 rounded-md font-medium transition-all inline-flex items-center justify-center gap-2 group">
                            Começar Gratuitamente
                            <svg class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                        <button class="text-base px-8 h-12 rounded-md font-medium border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground transition-all">
                            Agendar Demonstração
                        </button>
                    </div>

                    <p class="text-sm text-muted-foreground animate-fade-in-up" style="animation-delay: 0.4s;">
                        Utilizado por mais de <span class="font-semibold text-foreground">2.500+ advogados</span> em todo o Brasil
                    </p>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="recursos" class="py-20 md:py-32 bg-muted/30">
            <div class="container mx-auto px-4 md:px-6">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 text-balance">
                        Tudo que seu escritório precisa em uma plataforma
                    </h2>
                    <p class="text-lg text-muted-foreground text-balance">
                        Recursos completos para modernizar e escalar sua prática jurídica com eficiência máxima.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($features as $feature)
                    <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm p-6 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-border">
                        <div class="mb-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-secondary/10">
                                @if($feature['icon'] === 'clock')
                                    <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($feature['icon'] === 'file-search')
                                    <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                @elseif($feature['icon'] === 'file-text')
                                    <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                @elseif($feature['icon'] === 'dollar-sign')
                                    <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($feature['icon'] === 'users')
                                    <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                @elseif($feature['icon'] === 'bar-chart')
                                    <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                @elseif($feature['icon'] === 'shield')
                                    <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                @elseif($feature['icon'] === 'zap')
                                    <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-card-foreground">{{ $feature['title'] }}</h3>
                        <p class="text-muted-foreground leading-relaxed">{{ $feature['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Assistant Section -->
        <section id="assistente" class="py-20 md:py-32">
            <div class="container mx-auto px-4 md:px-6">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-secondary/10 border border-secondary/20">
                            <svg class="h-4 w-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            <span class="text-sm font-medium">Powered by Advanced AI</span>
                        </div>

                        <h2 class="text-3xl md:text-5xl font-bold text-balance">
                            Conheça o <span class="text-secondary">JurisTech Assist</span>
                        </h2>

                        <p class="text-lg text-muted-foreground leading-relaxed">
                            Nosso assistente de IA revolucionário que trabalha 24/7 para otimizar cada aspecto da sua prática jurídica. Treinado com milhões de documentos jurídicos para entregar resultados precisos e confiáveis.
                        </p>

                        <ul class="space-y-3">
                            @foreach($capabilities as $capability)
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-secondary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-foreground">{{ $capability }}</span>
                            </li>
                            @endforeach
                        </ul>

                        <button class="bg-secondary text-secondary-foreground hover:bg-secondary/90 text-base px-8 h-12 rounded-md font-medium transition-all inline-flex items-center justify-center gap-2">
                            Testar o Assistente
                        </button>
                    </div>

                    <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm p-8 md:p-12 bg-gradient-to-br from-primary/5 to-secondary/5 border-border relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"></div>

                        <div class="relative z-10 space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-secondary/20">
                                    <svg class="h-8 w-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">JurisTech Assist</h3>
                                    <p class="text-sm text-muted-foreground">Seu assistente jurídico IA</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-card p-4 rounded-lg border border-border">
                                    <p class="text-sm text-muted-foreground mb-2">Você perguntou:</p>
                                    <p class="text-foreground">"Analise este contrato e identifique possíveis riscos"</p>
                                </div>

                                <div class="bg-secondary/10 p-4 rounded-lg border border-secondary/20">
                                    <p class="text-sm text-secondary mb-2">JurisTech Assist respondeu:</p>
                                    <p class="text-foreground text-sm leading-relaxed">
                                        Análise concluída. Identifiquei 3 cláusulas que requerem atenção, incluindo uma cláusula de rescisão unilateral na seção 4.2 que pode expor seu cliente a riscos...
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <div class="flex -space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-primary/20 border-2 border-card"></div>
                                    <div class="w-8 h-8 rounded-full bg-secondary/20 border-2 border-card"></div>
                                    <div class="w-8 h-8 rounded-full bg-accent/20 border-2 border-card"></div>
                                </div>
                                <span>+2.500 advogados confiam no JurisTech Assist</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section id="beneficios" class="py-20 md:py-32 bg-gradient-to-b from-background to-muted/30">
            <div class="container mx-auto px-4 md:px-6">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 text-balance">Resultados que falam por si</h2>
                    <p class="text-lg text-muted-foreground text-balance">
                        Dados reais de escritórios que transformaram sua prática com JurisTech.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($benefits as $benefit)
                    <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm p-6 text-center hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border-border">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-secondary/10 mb-4">
                            @if($benefit['icon'] === 'trending-up')
                                <svg class="h-7 w-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @elseif($benefit['icon'] === 'award')
                                <svg class="h-7 w-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            @elseif($benefit['icon'] === 'clock')
                                <svg class="h-7 w-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($benefit['icon'] === 'users')
                                <svg class="h-7 w-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="text-4xl font-bold text-secondary mb-2">{{ $benefit['metric'] }}</div>
                        <div class="text-lg font-semibold mb-2 text-card-foreground">{{ $benefit['label'] }}</div>
                        <p class="text-sm text-muted-foreground leading-relaxed">{{ $benefit['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-20 md:py-32">
            <div class="container mx-auto px-4 md:px-6">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 text-balance">O que nossos clientes dizem</h2>
                    <p class="text-lg text-muted-foreground text-balance">
                        Depoimentos de advogados que transformaram sua prática jurídica.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($testimonials as $testimonial)
                    <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm p-6 border-border hover:shadow-lg transition-all duration-300">
                        <svg class="h-8 w-8 text-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <p class="text-card-foreground leading-relaxed mb-6 italic">"{{ $testimonial['quote'] }}"</p>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset($testimonial['image']) }}" alt="{{ $testimonial['author'] }}" class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <div class="font-semibold text-card-foreground">{{ $testimonial['author'] }}</div>
                                <div class="text-sm text-muted-foreground">{{ $testimonial['role'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="precos" class="py-20 md:py-32 bg-muted/30">
            <div class="container mx-auto px-4 md:px-6">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 text-balance">Planos para todos os tamanhos</h2>
                    <p class="text-lg text-muted-foreground text-balance">
                        Escolha o plano ideal para sua prática jurídica. Sem taxas ocultas, cancele quando quiser.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
                    @foreach($plans as $plan)
                    <div class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm p-8 relative hover:shadow-lg transition-all duration-300 {{ $plan['popular'] ? 'border-secondary border-2 shadow-lg scale-105' : 'border-border' }}">
                        @if($plan['popular'])
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span class="bg-secondary text-secondary-foreground px-4 py-1 rounded-full text-sm font-semibold">
                                Mais Popular
                            </span>
                        </div>
                        @endif

                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-card-foreground">{{ $plan['name'] }}</h3>
                            <p class="text-sm text-muted-foreground mb-4">{{ $plan['description'] }}</p>
                            <div class="flex items-end justify-center gap-1">
                                <span class="text-4xl font-bold text-card-foreground">{{ $plan['price'] }}</span>
                                @if($plan['period'])
                                <span class="text-muted-foreground mb-1">{{ $plan['period'] }}</span>
                                @endif
                            </div>
                        </div>

                        <ul class="space-y-3 mb-8">
                            @foreach($plan['features'] as $feature)
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-secondary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-card-foreground">{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>

                        <button class="w-full h-10 rounded-md px-6 font-medium transition-all {{ $plan['popular'] ? 'bg-secondary text-secondary-foreground hover:bg-secondary/90' : 'bg-primary text-primary-foreground hover:bg-primary/90' }}">
                            {{ $plan['cta'] }}
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 md:py-32 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-secondary/5 to-background"></div>
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>

            <div class="container mx-auto px-4 md:px-6 relative z-10">
                <div class="max-w-4xl mx-auto text-center space-y-8">
                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold text-balance">
                        Pronto para transformar sua prática jurídica?
                    </h2>
                    <p class="text-lg md:text-xl text-muted-foreground text-balance max-w-2xl mx-auto">
                        Junte-se a milhares de advogados que já estão usando JurisTech para trabalhar de forma mais inteligente e eficiente.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <button class="bg-secondary text-secondary-foreground hover:bg-secondary/90 text-base px-8 h-12 rounded-md font-medium transition-all inline-flex items-center justify-center gap-2 group">
                            Começar Teste Gratuito de 14 Dias
                            <svg class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                        <button class="text-base px-8 h-12 rounded-md font-medium border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground transition-all">
                            Agendar Demonstração
                        </button>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        Sem cartão de crédito • Cancelamento a qualquer momento • Suporte em português
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-primary/5 border-t border-border py-12 md:py-16">
            <div class="container mx-auto px-4 md:px-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-7 w-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            </svg>
                            <span class="text-xl font-bold">JurisTech</span>
                        </div>
                        <p class="text-sm text-muted-foreground leading-relaxed">
                            Transformando a prática jurídica com tecnologia de ponta e inteligência artificial.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-foreground">Produto</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a href="#recursos" class="text-muted-foreground hover:text-foreground transition-colors">
                                    Recursos
                                </a>
                            </li>
                            <li>
                                <a href="#assistente" class="text-muted-foreground hover:text-foreground transition-colors">
                                    JurisTech Assist
                                </a>
                            </li>
                            <li>
                                <a href="#precos" class="text-muted-foreground hover:text-foreground transition-colors">
                                    Preços
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                                    Integrações
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-foreground">Empresa</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                                    Sobre Nós
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                                    Blog
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                                    Carreiras
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                                    Contato
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-4 text-foreground">Contato</h3>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start gap-2 text-muted-foreground">
                                <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>contato@juristech.com.br</span>
                            </li>
                            <li class="flex items-start gap-2 text-muted-foreground">
                                <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>(11) 3000-0000</span>
                            </li>
                            <li class="flex items-start gap-2 text-muted-foreground">
                                <svg class="h-4 w-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>São Paulo, SP - Brasil</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="pt-8 border-t border-border">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <p class="text-sm text-muted-foreground">© 2026 JurisTech. Todos os direitos reservados.</p>
                        <div class="flex items-center gap-6 text-sm">
                            <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                                Privacidade
                            </a>
                            <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                                Termos de Uso
                            </a>
                            <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                                LGPD
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </main>
</body>
</html>
