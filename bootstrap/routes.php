<?php
$container = $app->getContainer();

$app->get('/', '\App\Controller\IndexController:index');


$app->group('/user', function() use ($app) {
    $app->post('/login', '\App\Controller\UserController:login');

    $app->post('/register', '\App\Controller\UserController:register');
});


$app->get('/test', '\App\Controller\IndexController:test')
    ->add(new \App\Middleware\SessionAuth($container));
