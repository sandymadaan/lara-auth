<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$router->post('register', [
    'as' => 'register',
    'uses' => 'AuthController@register',
]);

$router->post('login', [
    'as' => 'login',
    'uses' => 'AuthController@login',
]);

$router->group(['middleware' => ['auth:sanctum']], function ($router) {
    $router->get('/me', function(Request $request) {
        return auth()->user();
    });

    $router->post('logout', [
        'as' => 'logout',
        'uses' => 'AuthController@logout',
    ]);
});
