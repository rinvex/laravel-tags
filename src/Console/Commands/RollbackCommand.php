<?php

declare(strict_types=1);

namespace Rinvex\Tags\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'rinvex:rollback:tags')]
class RollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rinvex:rollback:tags {--f|force : Force the operation to run when in production.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback Rinvex Tags Tables.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->alert($this->description);

        $path = config('rinvex.tags.autoload_migrations') ?
            'vendor/rinvex/laravel-tags/database/migrations' :
            'database/migrations/rinvex/laravel-tags';

        if (file_exists($path)) {
            $this->call('migrate:reset', [
                '--path' => $path,
                '--force' => $this->option('force'),
            ]);
        } else {
            $this->warn('No migrations found! Consider publish them first: <fg=green>php artisan rinvex:publish:tags</>');
        }

        $this->line('');
    }
}
