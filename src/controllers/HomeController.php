<?php
namespace mywishlist\controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 * @author Jules Sayer <jules.sayer@protonmail.com>
 * @package mywishlist\controllers
 */
class HomeController extends Controller {

    public function showHome(Request $request, Response $response, array $args) : Response {
        $this->view->render($response, 'home.phtml', [
            "flash" => $this->flash->getMessages()
        ]);
        return $response;
    }
}