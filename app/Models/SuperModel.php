<?php

namespace App\Models;

use App\Observers\GlobalUUIDObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([GlobalUUIDObserver::class])]
class SuperModel extends Model
{
    use HasFactory;

    /** Necessário para usar unique UUID */
    public string $id = '';

    public bool $incrementing = false;
}
