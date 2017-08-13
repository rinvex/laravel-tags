<?php

declare(strict_types=1);

namespace Rinvex\Taggable\Console\Commands;

use Illuminate\Console\Command;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rinvex:migrate:taggable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Rinvex Taggable Tables.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->warn('Migrate rinvex/taggable:');
        $this->call('migrate', ['--step' => true, '--path' => 'vendor/rinvex/taggable/database/migrations']);
    }
}
