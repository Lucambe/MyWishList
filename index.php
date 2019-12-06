<?php
session_start();

require_once (__DIR__ . '/vendor/autoload.php');

\mywishlist\config\Database::connect();

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
$app = new \Slim\App($config);
$container = $app->getContainer();


/**
 * Setup PHP-View with Slim & add layout and global variables
 */
$container['view'] = function ($container) {
    $vars = [
        "rootUri" => $container->request->getUri()->getBasePath(),
        "router" => $container->router
    ];
    $renderer = new \Slim\Views\PhpRenderer(__DIR__ . '/src/views', $vars);
    $renderer->setLayout("layout.phtml");
    return $renderer;
};


/**
 * Routes
 */
$app->get('/', function ($request, $response, array $args) {
    $c = new \mywishlist\controllers\HomeController($this->view);
    return $c->showHome($request, $response, $args);
})->setName('home');


$app->get('/l/{token:[a-zA-Z0-9]+}/{id:[0-9]+}', function ($request, $response, array $args) {
    $c = new \mywishlist\controllers\ItemController($this->view);
    return $c->getItem($request, $response, $args);
})->setName('showItem');

$app->get('/l/{token:[a-zA-Z0-9]+}', function ($request, $response, array $args) {
    $c = new \mywishlist\controllers\ListeController($this->view);
    return $c->getListe($request, $response, $args);
})->setName('showList');

$app->get('/error/{n:[0-9]+}', function ($request, $response, array $args) {
    $c = new \mywishlist\controllers\ErrorController($this->view);
    return $c->showError($request, $response, $args);
})->setName('error');

$app->get('/about', function ($request, $response, array $args) {
    $this->view->render($response, 'about.phtml');
})->setName('about');

$app->get('/reservation', function ($request, $response, array $args) {
    $this->view->render($response, 'reservation.phtml');
})->setName('reservation');

$app->get('/reservation2', function ($request, $response, array $args) {
    $this->view->render($response, 'ReservationController.php');
})->setName('reservation2');

$app->get('/reserverItem/'/*{id:[0-9]+}'*/, function ($request, $response, array $args) {
    $c = new \mywishlist\controllers\ReservationController($this->view);
    return $c->reservItem($request, $response, $args);
})->setName('reserverItem');

// Run app
$app->run();