<?php

namespace App\Providers;

use App\Services\Processor;
use App\Services\ProcessorCacheDecorator;
use App\Services\ProcessorInterface;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProcessorInterface::class, Processor::class);
        $this->app->extend(ProcessorInterface::class, function (ProcessorInterface $processor) {
            return new ProcessorCacheDecorator(
                app()->make(Repository::class),
                $processor,
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
