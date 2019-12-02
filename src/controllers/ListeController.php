<?php
namespace mywishlist\controllers;

class ListeController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function getListe($request, $response, $args) {
        $liste = \mywishlist\models\Liste::where('no','=',$args['id'])->first();

        $this->view->render($response, 'liste.phtml', [
            "liste" => !is_null($liste) ? $liste : new \mywishlist\models\Liste()
        ]);
        return $response;
    }

    public function getListes($request, $response, $args) {
        $listes = \mywishlist\models\Liste::get();
        $this->view->render($response, 'listes.phtml', [
            "listes" => $listes
        ]);
        return $response;
    }
}