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

        \App::bind('Builder', '\Netinteractive\Elegant\Query\Builder');
        \App::bind('ElegantCollection', '\Netinteractive\Elegant\Model\Collection');
        \App::bind('ElegantRelationManager', '\Netinteractive\Elegant\Model\Relation\Manager');
        \App::bind('ElegantRelationDbTranslator', '\Netinteractive\Elegant\Model\Relation\Translator\DbTranslator');


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
