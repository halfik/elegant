<?php namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

/**
 * Class ElegantServiceProvider
 * @package Netinteractive\Elegant
 */
class FiltersServiceProvider extends ServiceProvider
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

        $this->publishes([
            __DIR__.'/../../config/filters.php' => config_path('/packages/netinteractive/elegant/filters.php'),
        ], 'config');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configFilters     = realpath(__DIR__.'/../../config/filters.php');

        $this->mergeConfigFrom($configFilters, 'packages.netinteractive.elegant.filters');

        \Event::listen('ni.elegant.record.after.fill', 'Netinteractive\Elegant\Model\Filter\Event\Handler@fillFilters');
        \Event::listen('ni.elegant.mapper.before.save', 'Netinteractive\Elegant\Model\Filter\Event\Handler@saveFilters');
        \Event::listen('ni.elegant.record.display', 'Netinteractive\Elegant\Model\Filter\Event\Handler@displayFilters');

        $this->app->booting(function()
        {
            AliasLoader::getInstance()->alias('DisplayFilter','Netinteractive\Elegant\Model\Filter\Loader');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['DisplayFilter'];
    }
    
}
