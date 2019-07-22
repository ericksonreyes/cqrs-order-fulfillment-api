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
$router->group(['prefix' => '/' . env('APP_VERSION') . '/v1/api'], function () use ($router) {
    $router->post('/auth', 'AuthenticationController@auth');
});

/**
 *
 * Secured end points.
 *
 */
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => '/' . env('APP_VERSION') . '/v1/api'], function () use ($router) {

        /**
         * Products
         */
        $router->get('/products', 'ProductsController@findAll');
        $router->post('/products/$id', 'ProductsController@findOne');

        /**
         * Orders
         */
        $router->post('/orders', 'OrdersController@findAll');
        $router->post('/orders/$id', 'OrdersController@findOne');
        $router->put('/orders/$id/accept', 'OrdersController@accept');
        $router->put('/orders/$id/ship', 'OrdersController@ship');
        $router->put('/orders/$id/cancel', 'OrdersController@cancel');
        $router->put('/orders/$id/complete', 'OrdersController@complete');
    });
});
