<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DBInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install fresh database instance and fill it with seeder data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('migrate:fresh', [], $this->output);
        Artisan::call('db:seed', [], $this->output);
    }
}
