<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\CsrfToken;
use App\Config\Env;

class UtilityController
{
    public function csrf(Request $req, Response $res): void
    {
        $token = CsrfToken::issue();
        $res->json(['csrf_token' => $token]);
    }

    public function ai(Request $req, Response $res): void
    {
        // Proxy to AI provider; placeholder to avoid embedding provider SDK
        $apiKey = Env::get('AI_API_KEY', '');
        if ($apiKey === '') {
            $res->json(['error' => 'AI not configured'], 503);
        }
        $prompt = (string) ($req->body['prompt'] ?? '');
        if ($prompt === '') {
            $res->json(['error' => 'Invalid prompt'], 400);
        }
        // For safety, return a stubbed response here. Real implementation would call the provider.
        $res->json({ 'answer': 'AI assistant is configured. Implement provider call here.' });
    }
}

