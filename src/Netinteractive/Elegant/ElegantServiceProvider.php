<?php namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

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
        \App::bind('ni.elegant.model.mapper.db', function($app, $params){
            return new \Netinteractive\Elegant\Model\Mapper\DbMapper($params[0]);
        });

        \App::bind('ni.elegant.db.query.builder', function($app, $params){
            $connection = \App::make('db')->connection(\Config::get('database.default'));

            $processor = $connection->getPostProcessor();
            $grammar = $connection->getQueryGrammar();

            return new \Netinteractive\Elegant\Db\Query\Builder($connection, $grammar, $processor);
        });

        \App::bind('ni.elegant.model.query.builder', function($app, $params){
            $connection = \App::make('db')->connection(\Config::get('database.default'));

            $processor = $connection->getPostProcessor();
            $grammar = $connection->getQueryGrammar();

            return new \Netinteractive\Elegant\Model\Query\Builder($connection, $grammar, $processor);
        });


        \App::bind('ni.elegant.model.collection', '\Netinteractive\Elegant\Model\Collection');

        \App::bind('ni.elegant.model.relation.manager', '\Netinteractive\Elegant\Model\Relation\Manager');
        \App::bind('ni.elegant.model.relation.translator.db', '\Netinteractive\Elegant\Model\Relation\Translator\DbTranslator');

        \App::bind('ni.elegant.search.db.translator', '\Netinteractive\Elegant\Search\Db\Translator');


        \App::make('ni.elegant.model.relation.manager')->registerTranslator('db', \App('ni.elegant.model.relation.translator.db'));


        if ($this->app->environment('testing')) {
            $this->app->register('Netinteractive\Elegant\FiltersServiceProvider');
        }
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
