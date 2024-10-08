<?php

namespace App\Models;

use App\Observers\GlobalUUIDObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo base com suporte a UUID e Factory.
 *
 * @property string $id
 */
#[ObservedBy([GlobalUUIDObserver::class])]
class SuperModel extends Model
{
    use HasFactory;
    use HasUuids;
}
