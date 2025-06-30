<?php

/** @var Router $router */

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

use App\Models\Catalog\Model;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    $data = [
        'name' => 'Новый продукт',
        'id_brand' => 115
    ];

    $validator = Validator::make($data, Model::rules(), Model::messages());

    if ($validator->fails()) {
        $errors = $validator->errors();
        var_dump($errors);
    } else {
        $brand = Model::create($validator->validated());
        var_dump($brand);
    }

    return $router->app->version();
});

$router->get('/catalog', 'CatalogController@index');
