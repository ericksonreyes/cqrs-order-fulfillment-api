<?php

namespace App\Console\Commands;

use App\Models\Command\EventModel;
use Elasticsearch\Client;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BuildElasticSearchEventStoreCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'acme:sales:elasticsearch_eventstore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recreates the ElasticSearch Event Store';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * BuildElasticSearchEventStoreCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->container = app()->get(ContainerInterface::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        try {
            $this->comment(' Acme ElasticSearch Event Store Builder. ');
            /**
             * @var $elasticsearch Client
             */
            $client = $this->container->get('elasticsearch_client');
            if ($client->indices()->exists(['index' => 'sales'])) {
                $this->line(' [*] Dropping ElasticSearch sales index. ');
                $client->indices()->delete(['index' => 'sales']);
            }

            $mysql = new EventModel();
            $id = 0;
            while (true) {
                $models = $mysql->where('id', '>', $id)->get();
                if (count($models) === 0) {
                    break;
                }
                foreach ($models as $model) {
                    $params = [
                        'index' => $this->indexName($model->context_name . ':' . $model->entity_type),
                        'type' => 'events',
                        'body' => [
                            'happenedOn' => $model->happened_on,
                            'entityContext' => $model->context_name,
                            'entityType' => $model->entity_type,
                            'entityId' => $model->entity_id,
                            'eventName' => $model->event_name,
                            'eventData' => $model->event_data,
                            'eventMetaData' => $model->event_meta_data
                        ]
                    ];
                    $client->index($params);
                    $id = $model->id;
                    $this->line(" [√] Indexed {$model->event_id}. ");
                }
            }
            $this->line(' Done!');
        } catch (Exception $e) {
            $this->line('');
            $this->error(
                ' An error occurred: ' . $e->getMessage() .
                "\n Exception: " . get_class($e) .
                "\n File: " . $e->getFile() .
                "\n Line: " . $e->getLine() .
                "\n Trace: " . $e->getTraceAsString()
            );
            $this->line('');
        }
    }


    /**
     * @param string $indexName
     * @return null|string|string[]
     */
    private function indexName(string $indexName)
    {
        return preg_replace('/[^A-Za-z0-9-_:]/', '', strtolower($indexName));
    }
}
