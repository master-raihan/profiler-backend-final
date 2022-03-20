<?php

/** @var \Laravel\Lumen\Routing\Router $router */

//Auth Routes
$router->post('/login','Admin\AuthController@login');

//File Routes
$router->post('/file/upload', 'Admin\FileController@uploadCsv');
$router->post('/file/process', 'Admin\FileController@processCsv');
$router->post('/files', 'Admin\FileController@test');

//User Routes
$router->get('/users','Admin\UserController@getAllUsers');
$router->post('/users/create','Admin\UserController@createUser');
$router->get('/users/edit/{id}','Admin\UserController@editUser');
$router->post('/users/update','Admin\UserController@updateUser');
$router->delete('/users/delete/{id}','Admin\UserController@deleteUser');

//Tag Routes
$router->get('/tags','Admin\TagController@getAllTags');
$router->post('/tags/create','Admin\TagController@createTag');
$router->delete('/tags/delete/{id}','Admin\TagController@deleteTag');


//Admin Routes
$router->get('/admins', 'AdminController@getAllAdmins');
// new
