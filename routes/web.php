<?php

use Laravel\Lumen\Routing\Router;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * @var $router Router
 */
$router->get('/', 'HomeController@index');

/**
 *
 * Public end points.
 *
 */
$router->group(['prefix' => '/' . env('APP_VERSION') . '/banking/rest'], function () use ($router) {
});


/**
 *
 * Secured end points.
 *
 */
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => '/' . env('APP_VERSION') . '/banking/rest'], function () use ($router) {
    });
});
