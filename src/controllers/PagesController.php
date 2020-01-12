<?php

namespace mywishlist\controllers;

use BadMethodCallException;
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

    /**
     * Affiche la page de connexion
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function showLogin(Request $request, Response $response, array $args): Response {
        try {
            if(isset($_SESSION['user'])) throw new BadMethodCallException("Vous êtes déjà connecté");
            $this->view->render($response, 'login.phtml', [
                "flash" => $this->flash->getMessages()
            ]);
        } catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    /**
     * Affiche la page d'inscription
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function showRegister(Request $request, Response $response, array $args): Response {
        try {
            if (isset($_SESSION['user'])) throw new BadMethodCallException("Vous êtes déjà connecté");
            $this->view->render($response, 'register.phtml', [
                "flash" => $this->flash->getMessages()
            ]);
        } catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    /**
     * Affiche la page de modification
     * du compte
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function showAccount(Request $request, Response $response, array $args): Response {
        try {
            if (!isset($_SESSION['user'])) throw new BadMethodCallException("Vous devez être connecté");
            $this->view->render($response, 'account.phtml', [
                "flash" => $this->flash->getMessages()
            ]);
        } catch (BadMethodCallException $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}