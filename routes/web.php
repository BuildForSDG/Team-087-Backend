<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('register', ['uses' => 'AuthController@register', 'as' => 'auth.register']);
        $router->get('verify', ['uses' => 'AuthController@verify', 'as' => 'auth.verify']);

        $router->post('signin', ['uses' => 'AuthController@signin', 'as' => 'auth.signin']);
        $router->post('refresh', ['uses' => 'AuthController@refresh', 'as' => 'auth.refresh']);
        $router->post('me', ['uses' => 'AuthController@me', 'as' => 'auth.me']);
        $router->post('signout', ['uses' => 'AuthController@signout', 'as' => 'auth.signout']);
    });
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});
