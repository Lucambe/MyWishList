<?php
require_once (__DIR__ . '/vendor/autoload.php');

\mywishlist\config\Database::connect();


$app = new \Slim\Slim();

$app->get('/', function () {
    echo "Hello, World !";
});

$app->get('/items', function() {
    $c = new \mywishlist\controllers\ItemController();
    $c->getItems();
});

$app->get('/item/:id', function($id) {
    $c = new \mywishlist\controllers\ItemController();
    $c->getItem($id);
});

$app->run();