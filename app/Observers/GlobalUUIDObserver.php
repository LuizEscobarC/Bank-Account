<?php

namespace App\Observers;

use App\Models\SuperModel;
use App\Services\UUIDService;

/**
 * Observer responsável por gerar UUIDs para modelos.
 */
class GlobalUUIDObserver
{
    /**
     * Cria uma nova instância do observer.
     *
     * @param \App\Services\UUIDService $uuidService Serviço para geração de UUIDs.
     */
    public function __construct(private readonly UUIDService $uuidService)
    {
    }

    /**
     * Manipula o evento de criação de um modelo.
     *
     * Gera e atribui um UUID ao modelo se ainda não tiver um ID.
     *
     * @param SuperModel $model Modelo a ser observado.
     * @return void
     */
    public function creating(SuperModel &$model)
    {
        if (empty($model->id)) {
            $model->id = $this->uuidService->generate();
        }
    }
}
