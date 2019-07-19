<?php

namespace App\Console;

use App\Console\Commands\AcmeProjectionGenerator;
use App\Console\Commands\BuildElasticSearchEventStoreCommand;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AcmeProjectionGenerator::class,
        BuildElasticSearchEventStoreCommand::class
    ];
}
