<?php namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

/**
 * Class TestServiceProvider
 * @package Netinteractive\Elegant
 */
class TestServiceProvider extends ServiceProvider
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
       
        $this->publishes(array(
            __DIR__.'/../../config/test.php' => config_path('/packages/netinteractive/elegant/test.php'),
        ), 'config');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../../config/test.php', 'netinteractive/elegant/test'
        );

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

}
