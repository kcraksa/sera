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
use Firebase\FirebaseLib;

$router->group(['prefix' => 'api'], function () use ($router)
{
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
    $router->post('/logincheck', 'AuthController@is_loggedin');

    $router->group(['middleware' => 'auth'], function() use ($router) {
        $router->post('/logout', 'AuthController@logout');

        // articles

        $router->get('/article', 'ArticleController@index');
        $router->post('/article/store', 'ArticleController@store');
        $router->get('/article/{id}/show', 'ArticleController@show');
        $router->post('/article/{id}/update', 'ArticleController@update');
        $router->post('/article/{id}/delete', 'ArticleController@delete');

        // end of articles

        // orders

        $router->get('/order', 'OrderController@index');
        $router->post('/order/store', 'OrderController@store');
        $router->get('/order/{id}/show', 'OrderController@show');
        $router->post('/order/{id}/update', 'OrderController@update');
        $router->post('/order/{id}/delete', 'OrderController@delete');

        // end of orders

    });

    // Other example

    $router->get('/soal_nomor_6_a', 'OtherController@soal_nomor_6_a');
    $router->get('/soal_nomor_6_b', 'OtherController@soal_nomor_6_b');
    $router->get('/soal_nomor_7', 'OtherController@soal_nomor_7');
});
