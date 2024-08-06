<?php

namespace App\Services\Data;

use Spatie\LaravelData\Data;

class ExternalAuthResponseData extends Data
{
    public bool $success;

    public bool $authorized;
}
