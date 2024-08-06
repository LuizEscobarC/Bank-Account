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
     * Register any application services.
     */
    public function register(): void
    {
        // Registra o serviço UUIDService
        $this->app->singleton(UUIDService::class, function ($app) {
            return new UUIDService();
        });

        $this->app->singleton(AccountBankService::class, function ($app) {
            return new AccountBankService();
        });

        // Registre o cliente de autenticação externo com o e-mail codificado
        $this->app->singleton(ExternalAuthClient::class, function ($app) {
            $email = 'luiz_escobar11@hotmail.com';

            return new ExternalAuthClient($email);
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService($app->make(ExternalAuthClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::preventLazyLoading(!app()->isProduction());

        // Registro do Observer Global de Model
        SuperModel::observe(GlobalUUIDObserver::class);
    }
}
