<?php

namespace App\Services\Data;

use Spatie\LaravelData\Data;

class ExternalAuthRequestData extends Data
{
    public int $sender;

    public int $receiver;

    public int $amount;
}
