<?php

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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->post('auth/register', ['uses' => 'AuthController@register', 'as' => 'auth.register']);
    $router->get('auth/verify', ['uses' => 'AuthController@verify', 'as' => 'auth.verify']);
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});
