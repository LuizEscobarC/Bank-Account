<?php

namespace App\Services\Data;

use Spatie\LaravelData\Data;

class ExternalAuthRequestData extends Data
{
    public function __construct(
        public string $sender,
        public string $receiver,
        public float $amount
    ) {
    }
}
