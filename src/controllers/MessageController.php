<?php


namespace mywishlist\controllers;


class MessageController extends Controller {

    /**
     * @todo: Fonction ajouter message a partir de données POST
     */
    public function addMessage($request, $response, $args) {
        $name = $request->getParsedBody()['name'];
        $message = $request->getParsedBody()['message'];
    }
}