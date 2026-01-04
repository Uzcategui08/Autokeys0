<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Inventario;
use App\Observers\InventarioObserver;
use App\Models\Producto;
use App\Observers\ProductoObserver;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\PeriodoCreado::class => [
            \App\Listeners\AsignarEmpleadosAlPeriodo::class,
        ],
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        Inventario::observe(InventarioObserver::class);
        Producto::observe(ProductoObserver::class);
    }
}
