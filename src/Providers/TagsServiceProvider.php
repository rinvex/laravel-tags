<?php

declare(strict_types=1);

namespace Rinvex\Tags\Providers;

use Rinvex\Tags\Models\Tag;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Rinvex\Tags\Console\Commands\MigrateCommand;
use Rinvex\Tags\Console\Commands\PublishCommand;
use Rinvex\Tags\Console\Commands\RollbackCommand;
use Illuminate\Database\Eloquent\Relations\Relation;

class TagsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class,
        PublishCommand::class,
        RollbackCommand::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.tags');

        // Bind eloquent models to IoC container
        $this->registerModels([
            'rinvex.tags.tag' => Tag::class,
        ]);

        // Register console commands
        $this->commands($this->commands);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // Register paths to be published by the publish command.
        $this->publishConfigFrom(__DIR__.'/../../config/config.php', 'rinvex/tags');
        $this->publishMigrationsFrom(__DIR__.'/../../database/migrations', 'rinvex/tags');

        ! $this->app['config']['rinvex.tags.autoload_migrations'] || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Map relations
        Relation::morphMap([
            'tag' => config('rinvex.tags.models.tag'),
        ]);
    }
}
