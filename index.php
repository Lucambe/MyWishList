<?php

use mywishlist\config\Database;
use mywishlist\controllers\AuthController;
use mywishlist\controllers\ItemController;
use mywishlist\controllers\ListeController;
use mywishlist\controllers\PagesController;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

require_once(__DIR__ . '/vendor/autoload.php');

session_start();

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
        "router" => $container->router,
        "user" => isset($_SESSION['user']) ? $_SESSION['user'] : null
    ];
    $renderer = new PhpRenderer(__DIR__ . '/src/views', $vars);
    $renderer->setLayout("layout.phtml");
    return $renderer;
};
$container['flash'] = function () {
    return new Messages();
};
$container['upload_directory'] = __DIR__ . '/public/images/';


/**
 * Accueil
 */
$app->get('/', function (Request $request, Response $response, array $args) use ($container) {
    $c = new PagesController($container);
    return $c->showHome($request, $response, $args);
})->setName('home');

/**
 * Pages annexe
 */
$app->get('/account', function (Request $request, Response $response, array $args) use ($container) {
    $c = new PagesController($container);
    return $c->showAccount($request, $response, $args);
})->setName('showAccount');

$app->post('/update/account', function (Request $request, Response $response, array $args) use ($container) {
    $c = new AuthController($container);
    return $c->updateAccount($request, $response, $args);
})->setName('updateAccount');

$app->post('/update/password', function (Request $request, Response $response, array $args) use ($container) {
    $c = new AuthController($container);
    return $c->updatePassword($request, $response, $args);
})->setName('updatePassword');

/**
 * Authentification
 */
$app->get('/register', function (Request $request, Response $response, array $args) use ($container) {
    $c = new PagesController($container);
    return $c->showRegister($request, $response, $args);
})->setName('showRegister');

$app->get('/login', function (Request $request, Response $response, array $args) use ($container) {
    $c = new PagesController($container);
    return $c->showLogin($request, $response, $args);
})->setName('showLogin');

$app->post('/register', function (Request $request, Response $response, array $args) use ($container) {
    $c = new AuthController($container);
    return $c->register($request, $response, $args);
})->setName('register');

$app->post('/login', function (Request $request, Response $response, array $args) use ($container) {
    $c = new AuthController($container);
    return $c->login($request, $response, $args);
})->setName('login');

$app->get('/logout', function (Request $request, Response $response, array $args) use ($container) {
    $c = new AuthController($container);
    return $c->logout($request, $response, $args);
})->setName('logout');

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

$app->post('/l/{token:[a-zA-Z0-9]+}/addMessage', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->addMessage($request, $response, $args);
})->setName('addMessage');

$app->post('/update/liste/{token:[a-zA-Z0-9]+}/{creationToken:[a-zA-Z0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->updateListe($request, $response, $args);
})->setName('updateListe');

$app->get('/delete/liste/{token:[a-zA-Z0-9]+}/{creationToken:[a-zA-Z0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->deleteListe($request, $response, $args);
})->setName('deleteListe');

$app->get('/showRes/{bool:[a-z]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ListeController($container);
    return $c->showRes($request, $response, $args);
})->setName('showRes');

/**
 * Objets
 */
$app->get('/l/{token:[a-zA-Z0-9]+}/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->getItem($request, $response, $args);
})->setName('showItem');

$app->post('/l/{token:[a-zA-Z0-9]+}/book/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->bookItem($request, $response, $args);
})->setName('bookItem');

$app->post('/create/item/{token:[a-zA-Z0-9]+}/{creationToken:[a-zA-Z0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->createItem($request, $response, $args);
})->setName('createItem');

$app->post('/update/item/{token:[a-zA-Z0-9]+}/{creationToken:[a-zA-Z0-9]+}/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->updateItem($request, $response, $args);
})->setName('updateItem');

$app->post('/uploadImg/item/{token:[a-zA-Z0-9]+}/{creationToken:[a-zA-Z0-9]+}/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->uploadImgItem($request, $response, $args);
})->setName('uploadImgItem');


$app->get('/update/item/{token:[a-zA-Z0-9]+}/{creationToken:[a-zA-Z0-9]+}/{id:[0-9]+}/deleteImage', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->deleteImgItem($request, $response, $args);
})->setName('deleteImageItem');

$app->get('/delete/item/{token:[a-zA-Z0-9]+}/{creationToken:[a-zA-Z0-9]+}/{id:[0-9]+}', function (Request $request, Response $response, array $args) use ($container) {
    $c = new ItemController($container);
    return $c->deleteItem($request, $response, $args);
})->setName('deleteItem');

// Run app
$app->run();