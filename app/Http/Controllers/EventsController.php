<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helper\Pagination;
use Elasticsearch\Client;
use Exception;
use Illuminate\Http\Request;

class EventsController extends Controller
{

    /**
     * @param Request $httpRequest
     * @param string $model
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \ReflectionException
     */
    public function all(Request $httpRequest, string $model)
    {
        try {
            $this->userMustHavePermissionTo('history', 'sales', $model);

            $page = $httpRequest->has('page') ? $httpRequest->get('page') : 1;
            $size = $httpRequest->has('size') ? $httpRequest->get('size') : 10;
            $offset = ($page > 0 ? $page - 1 : 0) * $size;

            $queryParams = [
                'index' => $this->indexName('sales' . ':' . $model),
                'type' => 'events',
                'from' => $offset,
                'size' => $size,
                'body' => [
                    'sort' => [
                        'happenedOn' => [
                            'order' => 'asc'
                        ]
                    ]
                ]
            ];

            if ($httpRequest->has('entityId')) {
                $queryParams['body']['query']['bool']['must'][]['term']['entityId.keyword'] =
                    $httpRequest->get('entityId');
            }

            if ($httpRequest->has('eventName')) {
                $queryParams['body']['query']['bool']['must'][]['term']['eventName.keyword'] =
                    $httpRequest->get('eventName');
            }

            if ($httpRequest->has('entityContext')) {
                $queryParams['body']['query']['bool']['must'][]['term']['entityContext.keyword'] =
                    $httpRequest->get('entityContext');
            }

            if ($httpRequest->has('entityType')) {
                $queryParams['body']['query']['bool']['must'][]['term']['entityType.keyword'] =
                    $httpRequest->get('entityType');
            }

            if ($httpRequest->has('happenedOn')) {
                $range = explode('and', strtolower($httpRequest->get('happenedOn')));

                if (count($range) === 2) {
                    $queryParams['body']['query']['bool']['filter']['range']['happenedOn']['gte'] =
                        (int)trim($range[0]);
                    $queryParams['body']['query']['bool']['filter']['range']['happenedOn']['lte'] =
                        (int)trim($range[1]);
                }
            }

            $data = $this->getEvents($queryParams);
            $totalNumberOfRecords = $this->countEvents($queryParams);

            $url = "/sales/api/events/{$model}";
            $responseArray = Pagination::paginate($data, $totalNumberOfRecords, $page, $size, $url);
            return $this->response(
                $responseArray,
                200
            );
        } catch (Exception $exception) {
            return $this->exception($exception);
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

    /**
     * @param $queryParams
     * @return array
     */
    private function getEvents($queryParams): array
    {
        /**
         * @var $client Client
         */
        $searchResult = $this->container()->get('elasticsearch_client')->search($queryParams);

        $data = [];
        if (isset($searchResult['hits']['hits'])) {
            foreach ($searchResult['hits']['hits'] as $hit) {
                $hit['_source']['eventData'] = json_decode($hit['_source']['eventData'], true);
                $hit['_source']['eventMetaData'] = json_decode($hit['_source']['eventMetaData'], true);
                $data[] = $hit['_source'];
            }
        }
        return $data;
    }

    /**
     * @param $queryParams
     * @return int
     */
    private function countEvents($queryParams): int
    {
        unset($queryParams['from'], $queryParams['size'], $queryParams['body']['sort']);
        $countResult = $this->container()->get('elasticsearch_client')->count($queryParams);
        return $countResult['count'] ?? 0;
    }
}
