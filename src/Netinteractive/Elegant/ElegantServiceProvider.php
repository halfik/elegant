<?php namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;

class ElegantServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\Event::listen('eloquent.elegant.before.setAttribute: *', 'Netinteractive\Elegant\Events\EventHandler@writeFilters');
		\Event::listen('eloquent.elegant.after.getAttribute: *', 'Netinteractive\Elegant\Events\EventHandler@readFilters');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
