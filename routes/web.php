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
        $router->post('me', ['uses' => 'AuthController@moi', 'as' => 'auth.moi']);
        $router->post('signout', ['uses' => 'AuthController@signout', 'as' => 'auth.signout']);
    });

    $router->group(['prefix' => 'users', 'middleware' => 'auth'], function () use ($router) {
        $router->get('appointments', ['uses' => 'AppointmentController@fetch', 'as' => 'appointments.fetch']);
        $router->get('me', ['uses' => 'UserController@view', 'as' => 'users.view']);//personal-profile

        $router->group(['prefix' => '{id}'], function () use ($router) {
            $router->post('reviews', ['uses' => 'ReviewController@add', 'as' => 'reviews.add']);
            $router->put('reviews/{reviewId}', ['uses' => 'ReviewController@edit', 'as' => 'reviews.edit']);
            $router->get('reviews', ['uses' => 'ReviewController@fetch', 'as' => 'reviews.fetch']);

            $router->post('appointments', ['uses' => 'AppointmentController@book', 'as' => 'appointments.book']);
            $router->get('appointments', ['uses' => 'AppointmentController@fetch', 'as' => 'appointments.fetch']);

            $router->get('', ['uses' => 'UserController@view', 'as' => 'users.view']);
        });

        $router->get('', ['uses' => 'UserController@fetch', 'as' => 'users.fetch']);
    });
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});
