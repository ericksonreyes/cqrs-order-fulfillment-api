<?php
/**
 * Created by PhpStorm.
 * User: ericksonreyes
 * Date: 2019-01-07
 * Time: 18:41
 */

namespace App\Console\Commands;

class AcmeProjectionGenerator extends RabbitMQDomainEventListener
{
    protected $signature = 'acme:sales:listen:projection_generator';

    protected $description = 'Creates projections from domain events.';

    /**
     * SalesApiProjectionGenerator constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->setEventProjectors(parent::container()->get('projection_generators'));
    }
}
