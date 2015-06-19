<?php namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;

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

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        \Event::listen('ni.elegant.record.fill', 'Netinteractive\Elegant\Model\Filter\Event\Handler@fillFilters');
        \Event::listen('ni.elegant.mapper.mapper.before.save', 'Netinteractive\Elegant\Model\Filter\Event\Handler@saveFilters');
        \Event::listen('ni.elegant.record.display', 'Netinteractive\Elegant\Model\Filter\Event\Handler@displayFilters');
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
