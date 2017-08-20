<?php

declare(strict_types=1);

namespace Rinvex\Taggable\Providers;

use Rinvex\Taggable\Models\Tag;
use Illuminate\Support\ServiceProvider;
use Rinvex\Taggable\Console\Commands\MigrateCommand;

class TaggableServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.rinvex.taggable.migrate',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.taggable');

        // Bind eloquent models to IoC container
        $this->app->singleton('rinvex.taggable.tag', function ($app) {
            return new $app['config']['rinvex.taggable.models.tag']();
        });
        $this->app->alias('rinvex.taggable.tag', Tag::class);

        // Register artisan commands
        foreach ($this->commands as $key => $value) {
            $this->app->singleton($value, function ($app) use ($key) {
                return new $key();
            });
        }

        $this->commands(array_values($this->commands));
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Load migrations
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

            // Publish Resources
            $this->publishResources();
        }
    }

    /**
     * Publish resources.
     *
     * @return void
     */
    protected function publishResources()
    {
        $this->publishes([realpath(__DIR__.'/../../config/config.php') => config_path('rinvex.taggable.php')], 'rinvex-taggable-config');
        $this->publishes([realpath(__DIR__.'/../../database/migrations') => database_path('migrations')], 'rinvex-taggable-migrations');
    }
}
