<?php namespace Netinteractive\Elegant;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Netinteractive\Elegant\Hashing\BcryptHasher;
use Netinteractive\Elegant\Hashing\NativeHasher;
use Netinteractive\Elegant\Hashing\Sha256Hasher;
use Netinteractive\Elegant\Hashing\WhirlpoolHasher;

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

        \App::bind('ni.elegant.mapper.db', function($app, $params){
            return new \Netinteractive\Elegant\Mapper\DbMapper($params[0]);
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
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $config     = realpath(__DIR__.'/../../config/config.php');
        $configFilters     = realpath(__DIR__.'/../../config/filters.php');

        $this->mergeConfigFrom($config, 'packages.netinteractive.elegant.config');
        $this->mergeConfigFrom($configFilters, 'packages.netinteractive.elegant.filters');
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
            $hasher = $app['config']->get('netinteractive.elegant.config.hasher');

            return \App::make($hasher);
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

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);


        $this->app['config']->set($key, array_merge_recursive(require $path, $config));
    }
}
