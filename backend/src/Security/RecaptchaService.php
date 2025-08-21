<?php
declare(strict_types=1);

namespace App\Security;

use GuzzleHttp\Client;
use App\Config\Env;

class RecaptchaService
{
    private Client $httpClient;
    private string $secret;

    public function __construct(?Client $client = null)
    {
        $this->httpClient = $client ?? new Client(['timeout' => 5]);
        $this->secret = Env::get('RECAPTCHA_SECRET_KEY', '');
    }

    public function verify(string $token, string $ip = ''): bool
    {
        if ($this->secret === '' || $token === '') {
            return false;
        }
        $resp = $this->httpClient->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => $this->secret,
                'response' => $token,
                'remoteip' => $ip,
            ],
        ]);
        $data = json_decode((string) $resp->getBody(), true);
        return isset($data['success']) && $data['success'] === true && ($data['score'] ?? 0) >= 0.3;
    }
}

