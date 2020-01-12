<?php

namespace mywishlist\controllers;

use DateTime;
use Exception;
use mywishlist\models\Liste;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 * @author Jules Sayer <jules.sayer@protonmail.com>
 * @package mywishlist\controllers
 */
class PagesController extends Controller {

    /**
     * Appel home.phtml, permet d'afficher les accueils
     * et les messages flash.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function showHome(Request $request, Response $response, array $args): Response {
        try {
            $this->view->render($response, 'home.phtml', [
                "flash" => $this->flash->getMessages(),
                "listes" => Liste::where('public', '=', 1)->whereDate('expiration', '>=', new DateTime())->orderBy('expiration', 'ASC')->get()
            ]);
        } catch (Exception $e) {}
        return $response;
    }

    public function showLogin(Request $request, Response $response, array $args): Response {
        $this->view->render($response, 'login.phtml', [
            "flash" => $this->flash->getMessages()
        ]);
        return $response;
    }

    public function showRegister(Request $request, Response $response, array $args): Response {
        $this->view->render($response, 'register.phtml', [
            "flash" => $this->flash->getMessages()
        ]);
        return $response;
    }

    public function showAccount(Request $request, Response $response, array $args): Response {
        $this->view->render($response, 'register.phtml', [
            "flash" => $this->flash->getMessages()
        ]);
        return $response;
    }
}