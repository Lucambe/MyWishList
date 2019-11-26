<?php
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
        "rootUri" => $container->request->getUri()->getBasePath()
    ];
    $renderer = new \Slim\Views\PhpRenderer(__DIR__ . '/src/views', $vars);
    $renderer->setLayout("layout.tpl");
    return $renderer;
};

/**
 * Routes
 */

$app->get('/', function ($request, $response, array $args) {
    return $this->view->render($response, 'home.tpl');
});


$app->get('/items', function ($request, $response, array $args) {
    $c = new \mywishlist\controllers\ItemController($this->router);
    $items = $c->getItems();
    return $this->view->render($response, 'items.tpl', [
        "items" => $items,
        "router" => $this->router
    ]);
});


// Run app
$app->run();