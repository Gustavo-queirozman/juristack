@php
    $isHome = request()->path() === '';
    $isDashboard = request()->routeIs('dashboard');
    $isPesquisa = request()->routeIs('datajud.index');
    $isSalvos = request()->routeIs('datajud.salvos') || request()->routeIs('datajud.salvo.show');
    $isClientes = request()->routeIs('clientes.*');
    $isProfile = request()->routeIs('profile.edit');
@endphp
<aside class="sidebar" aria-label="Navegação principal">
    <div class="sidebar-header">
        <a href="{{ url('/') }}" class="sidebar-brand">{{ config('app.name', 'JuriStack') }}</a>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ url('/') }}" class="sidebar-link {{ $isHome ? 'sidebar-link-active' : '' }}">
            <span class="sidebar-link-icon" aria-hidden="true">⌂</span>
            <span>Início</span>
        </a>
        @auth
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ $isDashboard ? 'sidebar-link-active' : '' }}">
            <span class="sidebar-link-icon" aria-hidden="true">▣</span>
            <span>Dashboard</span>
        </a>
        <div class="sidebar-group">
            <span class="sidebar-group-title">DataJud</span>
            <a href="{{ route('datajud.index') }}" class="sidebar-link {{ $isPesquisa ? 'sidebar-link-active' : '' }}">
                <span class="sidebar-link-icon" aria-hidden="true">🔍</span>
                <span>Pesquisa de processos</span>
            </a>
            <a href="{{ route('datajud.salvos') }}" class="sidebar-link {{ $isSalvos ? 'sidebar-link-active' : '' }}">
                <span class="sidebar-link-icon" aria-hidden="true">📁</span>
                <span>Processos salvos</span>
            </a>
        </div>
        <div class="sidebar-group">
            <span class="sidebar-group-title">Cadastros</span>
            <a href="{{ route('clientes.index') }}" class="sidebar-link {{ $isClientes ? 'sidebar-link-active' : '' }}">
                <span class="sidebar-link-icon" aria-hidden="true">👤</span>
                <span>Clientes</span>
            </a>
        </div>
        <div class="sidebar-group">
            <span class="sidebar-group-title">Conta</span>
            <a href="{{ route('profile.edit') }}" class="sidebar-link {{ $isProfile ? 'sidebar-link-active' : '' }} sidebar-link-muted">
                <span class="sidebar-link-icon" aria-hidden="true">⚙</span>
                <span>Configurações</span>
            </a>
        </div>
        @endauth
    </nav>
    @auth
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form" id="logout-form">
            @csrf
            <button type="button" class="sidebar-link sidebar-link-logout" id="logout-btn" aria-controls="logout-confirm-modal">
                <span class="sidebar-link-icon" aria-hidden="true">↪</span>
                <span>Sair</span>
            </button>
        </form>
    </div>
    @endauth
</aside>
