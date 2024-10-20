<?php

namespace App\Providers;

use App\Repositories\Ingredient\IIngredientRepository;
use App\Repositories\Ingredient\IngredientRepository;
use App\Repositories\Order\IOrderRepository;
use App\Repositories\Order\OrderRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IOrderRepository::class,OrderRepository::class);
        $this->app->bind(IIngredientRepository::class,IngredientRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
