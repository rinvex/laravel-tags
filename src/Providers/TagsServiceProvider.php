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
        // Publish Resources
        $this->publishesConfig('rinvex/laravel-tags');
        $this->publishesMigrations('rinvex/laravel-tags');
        ! $this->autoloadMigrations('rinvex/laravel-tags') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Map relations
        Relation::morphMap([
            'tag' => config('rinvex.tags.models.tag'),
        ]);
    }
}
