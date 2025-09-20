<?php

declare(strict_types=1);

namespace App\Support;

final class Csrf
{
    private SessionManager $session;

    public function __construct(SessionManager $session)
    {
        $this->session = $session;
    }

    public function token(): string
    {
        $token = $this->session->get('_csrf');
        if (!is_string($token)) {
            $token = bin2hex(random_bytes(16));
            $this->session->put('_csrf', $token);
        }

        return $token;
    }

    public function validate(?string $token): bool
    {
        return hash_equals($this->token(), (string) $token);
    }
}
