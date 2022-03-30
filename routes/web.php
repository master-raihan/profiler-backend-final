<?php

/** @var \Laravel\Lumen\Routing\Router $router */

//Auth Routes
$router->post('/admin/api/login','Admin\AuthController@login');
$router->post('/user/api/login','User\AuthController@login');


$router->group(['namespace' => 'User', 'prefix' => 'user/api','middleware' => 'auth:user'], function () use ($router) {
    //Contact Routes
    $router->get('/contacts/get-by-auth-user', 'ContactController@getContactsByUser');
    $router->get('/contacts/fields', 'ContactController@getFields');

    //Custom Fields Routes
    $router->post('/custom-fields/add-field','ContactController@addCustomField');
    $router->get('/custom-fields/get-by-auth-user', 'ContactController@getCustomFieldByUser');
    $router->post('/custom-fields/delete-by-auth-user','ContactController@deleteCustomField');

    //Filtering Query Routes
    $router->post('/contacts/filter','ContactController@filter');
});


$router->group(['namespace' => 'Admin', 'prefix' => 'admin/api','middleware' => 'auth:admin'], function () use ($router) {
    //File Routes
    $router->get('/files', 'FileController@getAllFiles');
    $router->post('/files/upload', 'FileController@uploadCsv');
    $router->post('/files/process', 'FileController@processCsv');

    //User Routes
    $router->get('/users','UserController@getAllUsers');
    $router->post('/users/create','UserController@createUser');
    $router->get('/users/get-user/{id}','UserController@getUserById');
    $router->post('/users/update','UserController@updateUser');
    $router->delete('/users/delete/{id}','UserController@deleteUser');

    //Tag Routes
    $router->get('/tags','TagController@getAllTags');
    $router->post('/tags/create','TagController@createTag');
    $router->delete('/tags/delete/{id}','TagController@deleteTag');
});


