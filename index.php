<?php

use mywishlist\config\Database;
use mywishlist\controllers\HomeController;
use mywishlist\controllers\ItemController;
use mywishlist\controllers\ListeController;
use mywishlist\controllers\MessageController;
use mywishlist\controllers\ReservationController;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
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
 * Accueil
 */
$app->get('/', function (Request $request, Response $response, array $args) use ($container) {
    $c = new HomeController($container);
    return $c->showHome($request, $response, $args);
})->setName('home');

/**
 * Pages annexe
 */
$app->get('/about', function (Request $request, Response $response, array $args) use ($container) {
    $this->view->render($response, 'about.phtml');
})->setName('showAbout');

/**
 * Authentification
 */
$app->get('/register', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'register.phtml');
})->setName('showRegister');

$app->get('/login', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'login.phtml');
})->setName('showLogin');

/**
 * Listes
 */
$app->get('/l/{token:[a-zA-Z0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->getListe($request, $response, $args);
})->setName('showListe');

$app->get('/l/{token:[a-zA-Z0-9]+}/admin/{creationToken:[a-zA-Z0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->getAdminListe($request, $response, $args);
})->setName('showAdminListe');

$app->get('/create/liste', function (Request $request, Response $response, array $args) {
    $this->view->render($response, 'createliste.phtml');
})->setName('showCreateListe');

$app->post('/create/liste', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->createListe($request, $response, $args);
})->setName('createListe');

$app->post('/addMessage', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->addMessage($request, $response, $args);
})->setName('addMessage');

$app->post('/update/liste/{token:[a-zA-Z0-9]+}/{creationToken:[a-zA-Z0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->updateListe($request, $response, $args);
})->setName('updateListe');

/**
 * Objets
 */
$app->get('/l/{token:[a-zA-Z0-9]+}/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->getItem($request, $response, $args);
})->setName('showItem');

$app->get('/i/{token:[a-zA-Z0-9]+}/create/{creationToken:[a-zA-Z0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->getCreateItem($request, $response, $args);
})->setName('showCreateItem');

$app->post('/book', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->bookItem($request, $response, $args);
})->setName('bookItem');

$app->post('/create/item', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->createItem($request, $response, $args);
})->setName('createItem');

$app->post('/modification', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->editItem($request, $response, $args);
})->setName('modifItem');

$app->post('/delete', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->deleteItem($request, $response, $args);
})->setName('deleteItem');

// Run app
$app->run();