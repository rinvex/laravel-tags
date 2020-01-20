<?php

declare(strict_types=1);

namespace Rinvex\Tags\Providers;

use Rinvex\Tags\Models\Tag;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Rinvex\Tags\Console\Commands\MigrateCommand;
use Rinvex\Tags\Console\Commands\PublishCommand;
use Rinvex\Tags\Console\Commands\RollbackCommand;

class TagsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.rinvex.tags.migrate',
        PublishCommand::class => 'command.rinvex.tags.publish',
        RollbackCommand::class => 'command.rinvex.tags.rollback',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.tags');

        // Bind eloquent models to IoC container
        $this->app->singleton('rinvex.tags.tag', $tagModel = $this->app['config']['rinvex.tags.models.tag']);
        $tagModel === Tag::class || $this->app->alias('rinvex.tags.tag', Tag::class);

        // Register console commands
        ! $this->app->runningInConsole() || $this->registerCommands();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // Publish Resources
        ! $this->app->runningInConsole() || $this->publishesConfig('rinvex/laravel-tags');
        ! $this->app->runningInConsole() || $this->publishesMigrations('rinvex/laravel-tags');
        ! $this->app['config']['rinvex.tags.autoload_migrations'] || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
