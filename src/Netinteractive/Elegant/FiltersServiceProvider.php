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
        $this->publishes([
            __DIR__.'/../../config/filters.php' => config_path('netinteractive/elegant/filters.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filters.php', 'ni-elegant-filters'
        );

        \Event::listen('ni.elegant.record.after.fill', 'Netinteractive\Elegant\Model\Filter\Event\Handler@fillFilters');
        \Event::listen('ni.elegant.mapper.before.save', 'Netinteractive\Elegant\Model\Filter\Event\Handler@saveFilters');
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
