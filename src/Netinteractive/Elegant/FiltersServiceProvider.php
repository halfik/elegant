<?php namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;

class FiltersServiceProvider extends ServiceProvider {

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
		$this->package('netinteractive/elegant');

		\Event::listen('eloquent.elegant.after.setAttribute: *', 'Netinteractive\Elegant\Events\EventHandler@fillFilters');
		\Event::listen('elegant.before.save', 'Netinteractive\Elegant\Events\EventHandler@saveFilters');
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
