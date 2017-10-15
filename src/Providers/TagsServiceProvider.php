<?php

declare(strict_types=1);

namespace Rinvex\Tags\Providers;

use Rinvex\Tags\Contracts\TagContract;
use Illuminate\Support\ServiceProvider;
use Rinvex\Tags\Console\Commands\MigrateCommand;
use Rinvex\Tags\Console\Commands\PublishCommand;

class TagsServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.rinvex.tags.migrate',
        PublishCommand::class => 'command.rinvex.tags.publish',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.tags');

        // Bind eloquent models to IoC container
        $this->app->singleton('rinvex.tags.tag', function ($app) {
            return new $app['config']['rinvex.tags.models.tag']();
        });
        $this->app->alias('rinvex.tags.tag', TagContract::class);

        // Register console commands
        ! $this->app->runningInConsole() || $this->registerCommands();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // Load migrations
        ! $this->app->runningInConsole() || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Publish Resources
        ! $this->app->runningInConsole() || $this->publishResources();
    }

    /**
     * Publish resources.
     *
     * @return void
     */
    protected function publishResources()
    {
        $this->publishes([realpath(__DIR__.'/../../config/config.php') => config_path('rinvex.tags.php')], 'rinvex-tags-config');
        $this->publishes([realpath(__DIR__.'/../../database/migrations') => database_path('migrations')], 'rinvex-tags-migrations');
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        // Register artisan commands
        foreach ($this->commands as $key => $value) {
            $this->app->singleton($value, function ($app) use ($key) {
                return new $key();
            });
        }

        $this->commands(array_values($this->commands));
    }
}
