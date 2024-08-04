<?php

namespace App\Services;

use Illuminate\Support\Str;

class UUIDService
{
    /**
     * Gera um UUID v4.
     *
     * @return string
     */
    public function generate(): string
    {
        return (string) Str::uuid();
    }
}
