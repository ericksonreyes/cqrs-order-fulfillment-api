<?php

namespace App\Http\Controllers;

use Exception;

class HomeController extends Controller
{

    private const SECONDS = 60;
    private const MINUTES = 60;
    private const HOURS = 24;

    public function index()
    {
        try {
            $days = 7;
            $secondsToCache = $days * (self::SECONDS * self::MINUTES * self::HOURS);
            $timestamp = gmdate('D, d M Y H:i:s', time() + $secondsToCache) . ' GMT';
            header("Expires: {$timestamp}", true);
            header('Pragma: cache', true);
            header("Cache-Control: max-age={$secondsToCache}", true);
            header('Content-Type: text/yaml', true);
            $contents = file_get_contents(base_path('swagger.yml'));
            $trimmedContents = trim($contents);
            $contentsWithNoMultipleNewlines = preg_replace("/[\r\n]+/", "\n", $trimmedContents);
            echo($contentsWithNoMultipleNewlines);
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }
}
