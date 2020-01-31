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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


// Produk CRUD
Route::group(['middleware' => ['auth']], function($router){
    $router->get('/produk', 'ProdukController@index');
    $router->get('/produk/{id}', 'ProdukController@show');
    $router->post('/produk', 'ProdukController@store');
    $router->put('/produk/{id}', 'ProdukController@update');
    $router->delete('/produk/{id}', 'ProdukController@destroy');
});

// Supplier CRUD
Route::group(['middleware' => ['auth']], function($router){
    $router->get('/supplier', 'SupplierController@index');
    $router->get('/supplier/{id}', 'SupplierController@show');
    $router->post('/supplier', 'SupplierController@store');
    $router->put('/supplier/{id}', 'SupplierController@update');
    $router->delete('/supplier/{id}', 'SupplierController@destroy');
});

// Users CRUD
Route::group(['middleware' => ['auth']], function($router){
    $router->post('users', 'UsersController@store');
    $router->get('/users', 'UsersController@index');
    $router->get('/users/{id}', 'UsersController@show');
    $router->put('/users/{id}', 'UsersController@update');
    $router->delete('/router/{id}', 'UsersController@delete');
});

// Transaksi
Route::group(['middleware' => ['auth']], function($router){
    $router->post('transaksi', 'TransaksiController@store');
    $router->get('/transaksi', 'TransaksiController@index');
    $router->get('/transaksi/{id}', 'TransaksiController@show');
    $router->put('/transaksi/{id}', 'TransaksiController@update');
    $router->delete('/transaksi/{id}', 'TransaksiController@delete');
});


// auth
$router->group(['prefix' => 'auth'], function() use ($router){
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});