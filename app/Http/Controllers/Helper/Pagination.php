<?php

namespace App\Http\Controllers\Helper;

class Pagination
{


    /**
     * @param array $data
     * @param $totalNumberOfRecords
     * @param $size
     * @param $page
     * @param $url
     * @return mixed
     */
    public static function paginate(array $data, int $totalNumberOfRecords, $page, $size, $url)
    {
        $response['_embedded'] = $data;
        $pages = ceil($totalNumberOfRecords / $size);
        $response['_page']['size'] = (string)$size;
        $response['_page']['number'] = (string)$page;
        $response['_page']['pages'] = (string)$pages;
        $response['_count'] = (string)count($response['_embedded']);
        $response['_total'] = (string)$totalNumberOfRecords;


        $next = $page + 1;
        $prev = $page - 1;
        $first = 1;
        $last = $pages;

        $response['_links'][] = [
            'rel' => 'self',
            'href' => url("{$url}?page={$page}&size={$size}"),
            'type' => 'GET',
            'title' => 'Current'
        ];

        if ($next <= $pages) {
            $response['_links'][] = [
                'rel' => 'next',
                'href' => url("{$url}?page={$next}&size={$size}"),
                'type' => 'GET',
                'title' => 'Next'
            ];
        }

        if ($prev > 0) {
            $response['_links'][] = [
                'rel' => 'prev',
                'href' => url("{$url}?page={$prev}&size={$size}"),
                'type' => 'GET',
                'title' => 'Previous'
            ];
        }

        if ($pages > 1) {
            if ($page > 1) {
                $response['_links'][] = [
                    'rel' => 'first',
                    'href' => url("{$url}?page={$first}&size={$size}"),
                    'type' => 'GET',
                    'title' => 'First'
                ];
            }
            if ($page < $pages) {
                $response['_links'][] = [
                    'rel' => 'last',
                    'href' => url("{$url}?page={$last}&size={$size}"),
                    'type' => 'GET',
                    'title' => 'Last'
                ];
            }
        }
        return $response;
    }
}
