<?php namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;

/**
 * Class ElegantServiceProvider
 * @package Netinteractive\Elegant
 */
class ElegantServiceProvider extends ServiceProvider
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
        \App::bind('ElegantQueryBuilder', '\Netinteractive\Elegant\Db\Query\Builder');
        \App::bind('ElegantModelQueryBuilder', '\Netinteractive\Elegant\Model\Query\Builder');
        \App::bind('ElegantCollection', '\Netinteractive\Elegant\Model\Collection');

        \App::bind('ElegantRelationManager', '\Netinteractive\Elegant\Model\Relation\Manager');
        \App::bind('ElegantRelationDbTranslator', '\Netinteractive\Elegant\Model\Relation\Translator\DbTranslator');

        \App::bind('ElegantSearchDbTranslator', '\Netinteractive\Elegant\Search\Db\Translator');


        \App::make('ElegantRelationManager')->registerTranslator('db', \App('ElegantRelationDbTranslator'));

        \App::bind('ElegantDbMapper', function($app, $params){
            return new \Netinteractive\Elegant\Model\Mapper\DbMapper($params[0]);
        });
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
