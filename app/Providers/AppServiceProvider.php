<?php

namespace App\Providers;

use App\Models\SuperModel;
use App\Observers\GlobalUUIDObserver;
use App\Services\Auth\AuthService;
use App\Services\Auth\Clients\ExternalAuthClient;
use App\Services\{
    AccountBankService,
    UUIDService
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra os serviços da aplicação.
     *
     * @return void
     */
    public function register(): void
    {
        // Registra o serviço UUIDService como singleton
        $this->app->singleton(UUIDService::class, function ($app) {
            return new UUIDService();
        });

        // Registra o serviço AccountBankService como singleton
        $this->app->singleton(AccountBankService::class, function ($app) {
            return new AccountBankService();
        });

        // Registra o cliente de autenticação externo com o e-mail codificado
        $this->app->singleton(ExternalAuthClient::class, function ($app) {
            $email = 'luiz_escobar11@hotmail.com';

            return new ExternalAuthClient($email);
        });

        // Registra o serviço AuthService como singleton
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService($app->make(ExternalAuthClient::class));
        });
    }

    /**
     * Inicializa os serviços da aplicação.
     *
     * @return void
     */
    public function boot(): void
    {
        // Desprotege os modelos para acesso direto aos atributos
        Model::unguard();

        // Desativa o carregamento preguiçoso fora do ambiente de produção
        Model::preventLazyLoading(!app()->isProduction());

        // Registra o observer global para o modelo SuperModel
        SuperModel::observe(GlobalUUIDObserver::class);
    }
}
