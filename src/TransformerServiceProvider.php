<?php namespace KamranAhmed\LaraFormer;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class TransformerServiceProvder extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware(TransformerMiddleware::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register 'laraformer' instance container to our UnderlyingClass object
        $this->app['laraformer'] = $this->app->share(function ($app) {
            return new Transformer();
        });

        // So one doesn't need to add an Alias in app/config/app.php
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('LaraFormer', 'KamranAhmed\LaraFormer\Facades\Transformer');
        });
    }
}
