<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventCacheForAuth
{
    /**
     * Impede que páginas acessadas com autenticação fiquem no cache do navegador.
     * Assim, ao clicar em "Voltar" após logout, o navegador revalida e o servidor
     * redireciona para a tela de login em vez de exibir a página em cache.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
