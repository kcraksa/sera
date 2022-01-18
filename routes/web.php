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

use App\Models\Customers;

$router->get('/', function () use ($router) {
    // return $router->app->version();
    return Customers::all();
});

$router->group(['prefix' => 'api'], function () use ($router)
{
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
    $router->post('/logincheck', 'AuthController@is_loggedin');

    $router->group(['middleware' => 'auth'], function() use ($router) {
        $router->post('/logout', 'AuthController@logout');
    });
});
