<?php

namespace App\Observers;

use App\Models\SuperModel;
use App\Services\UUIDService;

class GlobalUUIDObserver
{
    protected $uuidService;

    /**
     * Observer criado para implementar a lógica de UUIDService nos models
     */
    public function __construct(UUIDService $uuidService)
    {
        $this->uuidService = $uuidService;
    }

    /**
     * Handle para o evento de "criação" de qualquer modelo.
     * @param  SuperModel  $model
     * @return void
     */
    public function creating(SuperModel &$model)
    {
        if (empty($model->id)) {
            $model->id = $this->uuidService->generate();
        }
    }
}
