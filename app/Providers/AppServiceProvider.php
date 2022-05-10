<?php

namespace App\Providers;

use App\Contracts\Services\CrudServiceInterface;
use App\Contracts\Services\LpSaveInstantInterface;
use App\Contracts\Services\Select2ServiceInterface;
use App\Services\Crud\CrudService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CrudServiceInterface::class, function ($app, $parameter = []) {
            return new CrudService();
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
