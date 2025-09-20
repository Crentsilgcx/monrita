<?php

declare(strict_types=1);

namespace App\Support;

final class ProfanityFilter
{
    private array $blocked = ['badword'];

    public function isClean(string $message): bool
    {
        $lower = strtolower($message);
        foreach ($this->blocked as $word) {
            if (str_contains($lower, $word)) {
                return false;
            }
        }

        return true;
    }
}
