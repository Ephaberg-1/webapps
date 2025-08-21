<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Env;

class Mailer
{
    public function send(string $to, string $subject, string $htmlBody): bool
    {
        $from = Env::get('MAIL_FROM', 'noreply@example.com');
        $headers = "MIME-Version: 1.0\r\n" .
            "Content-type:text/html;charset=UTF-8\r\n" .
            "From: {$from}\r\n";
        return mail($to, $subject, $htmlBody, $headers);
    }
}

