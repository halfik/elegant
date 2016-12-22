<?php

namespace Netinteractive\Elegant;

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

use Illuminate\Support\ServiceProvider;
use Netinteractive\Elegant\Db\Connectors\ConnectionFactory;
use Netinteractive\Elegant\Db\DatabaseManager;

/**
 * Class DatabaseServiceProvider
 * @package Netinteractive\Elegant
 */
class DatabaseServiceProvider extends ServiceProvider
{
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
        $this->registerFactories();

        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });

        $this->app->bind('db.connection', function ($app) {
            return $app['db']->connection();
        });
    }

    /**
     * Register the Eloquent factory instance in the container.
     *
     * @return void
     */
    protected function registerFactories()
    {
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create();
        });
    }

}
