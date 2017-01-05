<?php

namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;
use Netinteractive\Elegant\Console\Commands\MakeElegant;


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

    protected $commands = [
        'Netinteractive\Elegant\Console\Commands\MakeElegant',
    ];


	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('/packages/netinteractive/elegant/config.php'),
        ], 'config');

        $migrations = realpath(__DIR__.'/../../migrations');

        $this->publishes([
            $migrations => $this->app->databasePath().'/migrations',
        ]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->prepareResources();
        $this->registerHasher();
        $this->registerFieldGenerator();

        \App::bind('ni.elegant.repository', function($app, $params){
            return new \Netinteractive\Elegant\Repository\Repository($params[0]);
        });
        $db = \App::make('db');

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

        $this->commands($this->commands);
	}

    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $config     = realpath(__DIR__.'/../../config/config.php');

        $this->mergeConfigFrom($config, 'packages.netinteractive.elegant.config');
    }

    /**
     * Register the hasher used by Sentry.
     *
     * @return void
     */
    protected function registerHasher()
    {
        $this->app['elegant.hasher'] = $this->app->share(function($app)
        {
            $hasher = \Config::get('packages.netinteractive.elegant.config.hasher');

            return \App::make($hasher);
        });
    }

    /**
     *  Register factory of field generators
     */
    protected function registerFieldGenerator()
    {
        $this->app->singleton('elegant.make.fieldGenerator', function ($app) {
            return new Utils\FieldGenerator();
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
