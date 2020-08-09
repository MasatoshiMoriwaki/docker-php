<?php
return [
    'system_root_dir' => '/work',

    'namespace_controller' => 'App\\Http\\Controllers\\',

    'web' => [
        //*** [method, path, Controller@function (, parameter)] ***//
        ['GET',  '/', 'TopController@index'],
        ['GET',  '/search', 'Junkissa/SearchController@index'],
        ['GET',  '/junkissa/:junkissa_id', 'Junkissa/JunkissaController@index', ['junkissa_id' => URL_PARAM_TYPE_NUM]],
        ['GET',  '/junkissa/:junkissa_id/edit', 'Junkissa/JunkissaController@edit', ['junkissa_id' => URL_PARAM_TYPE_NUM]],
        ['POST', '/junkissa/:junkissa_id/edit', 'Junkissa/JunkissaController@save', ['junkissa_id' => URL_PARAM_TYPE_NUM]],
        ['GET',  '/junkissa/:junkissa_id/images/edit', 'Junkissa/JunkissaController@editImages', ['junkissa_id' => URL_PARAM_TYPE_NUM]],
        ['POST', '/junkissa/:junkissa_id/images/edit', 'Junkissa/JunkissaController@saveImages', ['junkissa_id' => URL_PARAM_TYPE_NUM]],
        ['GET',  '/junkissa/new', 'Junkissa/JunkissaController@edit'],
        ['POST', '/junkissa/new', 'Junkissa/JunkissaController@save'],
        ['GET',  '/login', 'Auth/AuthController@showLoginForm'],
        ['POST', '/login', 'Auth/AuthController@login'],
        ['POST', '/register', 'Auth/AuthController@register'],
        ['GET',  '/logout', 'Auth/AuthController@logout'],
        ['GET',  '/user/:user_id', 'User/UserController@index', ['user_id' => URL_PARAM_TYPE_NUM]],
        ['GET',  '/user/me/edit', 'User/UserController@edit'],
        ['POST', '/user/me/edit', 'User/UserController@save'],
        ['GET',  '/email/change', 'User/UserController@changeEmail'],
        ['POST', '/email/change', 'User/UserController@sendConfirmationEmail'],
        ['GET',  '/email/change/verify', 'User/UserController@activateNewEmail'],
    ],
];