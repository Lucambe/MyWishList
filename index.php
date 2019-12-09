<?php

use mywishlist\config\Database;
use mywishlist\controllers\ErrorController;
use mywishlist\controllers\HomeController;
use mywishlist\controllers\ItemController;
use mywishlist\controllers\ListeController;
use mywishlist\controllers\ReservationController;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Views\PhpRenderer;

session_start();

require_once (__DIR__ . '/vendor/autoload.php');

Database::connect();

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
 * Setup PHP-View with Slim & add layout and global variables
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

/**
 * Setup Flash Message with Slim
 */
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

$app->get('/error/{n:[0-9]+}', function ($request, $response, array $args) {
    global $container;
    $c = new ErrorController($container);
    return $c->showError($request, $response, $args);
})->setName('error');

$app->get('/about', function ($request, $response, array $args) {
    $this->view->render($response, 'about.phtml');
})->setName('about');

$app->post('/book', function ($request, $response, $args) {
    global $container;
    $c = new ReservationController($container);
    return $c->bookItem($request, $response, $args);
})->setName('book');

// Run app
$app->run();