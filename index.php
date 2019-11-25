<?php
require_once (__DIR__ . '/vendor/autoload.php');

$db = new \Illuminate\Database\Capsule\Manager();
if(file_exists("src/config/database.ini")) {
    $db->addConnection(parse_ini_file("src/config/database.ini"));
    $db->setAsGlobal();
    $db->bootEloquent();
} else {
    throw new Exception('Le fichier src/config/database.ini n\'existe pas');
}

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