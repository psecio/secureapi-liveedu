<?php

require_once __DIR__.'/../vendor/autoload.php';
define('BASE_PATH', __DIR__.'/../');
define('APP_PATH', __DIR__.'/../App');

// Load in the .env file
$dotenv = new Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();

spl_autoload_register(function($class) {
    $path = str_replace('\\', '/', $class).'.php';
    if (!is_file(BASE_PATH.$path)) {
        throw new \Exception('Invalid class: '.$class);
    }
    require_once BASE_PATH.$path;
});

// Load everything in bootstrap
$dir = new \DirectoryIterator(BASE_PATH.'/bootstrap');
foreach ($dir as $file) {
    if (!$file->isDot()) {
        require_once $file->getPathname();
    }
}

$container = $app->getContainer();

// Load all of the controllers
$dir = new \DirectoryIterator(APP_PATH.'/Controller');
foreach ($dir as $file) {
    if (!$file->isDot()) {
        $ns = '\\App\\Controller\\'.str_replace('.php', '', $file->getFilename());
        $container[$ns] = function() use ($ns, $container) {
            return new $ns($container);
        };
    }
}

$container['log'] = function() {
    return new \App\Lib\Logger();
};

// Lets go!
$app->run();
