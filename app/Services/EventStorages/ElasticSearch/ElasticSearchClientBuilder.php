<?php

namespace App\Services\EventStorages\ElasticSearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticSearchClientBuilder
{
    /**
     * @param array $hosts
     * @return Client
     */
    public static function build(array $hosts): Client
    {
        return ClientBuilder::create()->setHosts($hosts)->build();
    }
}
