<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function __construct(private readonly WhatsAppChatbotService $chatbotService) {}

    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return response()->json(['message' => 'Unauthorized webhook.'], 403);
        }

        $result = $this->chatbotService->handle($request->all());

        return response()->json($result);
    }

    private function isAuthorized(Request $request): bool
    {
        $expectedToken = trim((string) config('services.whatsapp.token', ''));

        if ($expectedToken === '') {
            return false;
        }

        $authorization = (string) $request->header('Authorization', '');
        $bearerToken = str_starts_with($authorization, 'Bearer ')
            ? substr($authorization, 7)
            : null;

        return hash_equals($expectedToken, (string) $request->query('token'))
            || hash_equals($expectedToken, (string) $request->header('X-Webhook-Token'))
            || hash_equals($expectedToken, (string) $request->header('X-WhatsApp-Token'))
            || ($bearerToken !== null && hash_equals($expectedToken, $bearerToken));
    }
}
