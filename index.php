<?php

use mywishlist\config\Database;
use mywishlist\controllers\HomeController;
use mywishlist\controllers\ItemController;
use mywishlist\controllers\ListeController;
use mywishlist\controllers\MessageController;
use mywishlist\controllers\ReservationController;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Views\PhpRenderer;

session_start();

require_once (__DIR__ . '/vendor/autoload.php');

try {
    Database::connect();
} catch (Exception $e) {
    die($e->getMessage());
}

/**
 * Dev. mode to show errors in details
 */
$config = [
    'settings' => [
        'displayErrorDetails' => 1,
    ],
];

/**
 * Instanciate Slim
 */
$app = new App($config);
$container = $app->getContainer();


/**
 * Setup container using PhpRenderer & Flash Messages
 */
$container['view'] = function ($container) {
    $vars = [
        "rootUri" => $container->request->getUri()->getBasePath(),
        "router" => $container->router
    ];
    $renderer = new PhpRenderer(__DIR__ . '/src/views', $vars);
    $renderer->setLayout("layout.phtml");
    return $renderer;
};
$container['flash'] = function () {
    return new Messages();
};


/**
 * Routes
 */
$app->get('/', function ($request, $response, array $args) {
    global $container;
    $c = new HomeController($container);
    return $c->showHome($request, $response, $args);
})->setName('home');


$app->get('/l/{token:[a-zA-Z0-9]+}/{id:[0-9]+}', function ($request, $response, array $args) {
    global $container;
    $c = new ItemController($container);
    return $c->getItem($request, $response, $args);
})->setName('showItem');

$app->get('/l/{token:[a-zA-Z0-9]+}', function ($request, $response, array $args) {
    global $container;
    $c = new ListeController($container);
    return $c->getListe($request, $response, $args);
})->setName('showList');

$app->get('/about', function ($request, $response, array $args) {
    $this->view->render($response, 'about.phtml');
})->setName('about');

$app->post('/book', function ($request, $response, $args) {
    global $container;
    $c = new ReservationController($container);
    return $c->bookItem($request, $response, $args);
})->setName('book');

$app->post('/message', function ($request, $response, $args) {
    global $container;
    $c = new MessageController($container);
    return $c->addMessage($request, $response, $args);
})->setName('message');

$app->get('/newliste', function ($request, $response, array $args) {
    $this->view->render($response, 'newliste.phtml');
})->setName('newliste');

// Run app
$app->run();