<?php
declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use App\Config\Env;

class PaystackService
{
    private Client $httpClient;
    private string $secretKey;

    public function __construct(?Client $client = null)
    {
        $this->httpClient = $client ?? new Client(['timeout' => 10, 'base_uri' => 'https://api.paystack.co/']);
        $this->secretKey = Env::get('PAYSTACK_SECRET_KEY', '');
    }

    public function initialize(string $email, int $amountKobo, string $reference, string $callbackUrl): array
    {
        $resp = $this->httpClient->post('transaction/initialize', [
            'headers' => ['Authorization' => 'Bearer ' . $this->secretKey],
            'json' => [
                'email' => $email,
                'amount' => $amountKobo,
                'reference' => $reference,
                'callback_url' => $callbackUrl,
            ],
        ]);
        return json_decode((string) $resp->getBody(), true);
    }

    public function verify(string $reference): array
    {
        $resp = $this->httpClient->get('transaction/verify/' . rawurlencode($reference), [
            'headers' => ['Authorization' => 'Bearer ' . $this->secretKey],
        ]);
        return json_decode((string) $resp->getBody(), true);
    }
}

