<?php

namespace App\Console;

use App\Console\Commands\BuildElasticSearchEventStoreCommand;
use App\Console\Commands\ProjectionGenerator;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ProjectionGenerator::class
    ];
}
