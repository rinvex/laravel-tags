<?php

declare(strict_types=1);

namespace Rinvex\Tags\Console\Commands;

use Illuminate\Console\Command;

class MigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rinvex:migrate:tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Rinvex Tags Tables.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->warn('Migrate rinvex/tags:');
        $this->call('migrate', ['--step' => true, '--path' => 'vendor/rinvex/tags/database/migrations']);
    }
}
