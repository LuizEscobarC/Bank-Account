<?php

namespace App\Services\Data;

use Spatie\LaravelData\Data;

class ExternalAuthRequestData extends Data
{
    public function __construct(
        public int $sender,
        public int $receiver,
        public float $amount
    ) {
    }
}
