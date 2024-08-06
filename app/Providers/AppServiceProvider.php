<?php

namespace App\Providers;

use App\Models\SuperModel;
use App\Observers\GlobalUUIDObserver;
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
