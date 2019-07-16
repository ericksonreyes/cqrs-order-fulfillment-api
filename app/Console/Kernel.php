<?php

namespace App\Console;

use App\Console\Commands\AcmeProjectionGenerator;
use App\Console\Commands\BuildElasticSearchEventStoreCommand;
use App\Console\Commands\SalesApiEmailNotificationSender;
use App\Console\Commands\SalesApiProjectionGeneratorAlias;
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
        SalesApiEmailNotificationSender::class,
        BuildElasticSearchEventStoreCommand::class
    ];
}
