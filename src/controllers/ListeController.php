<?php
namespace mywishlist\controllers;

class ListeController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function getListe($request, $response, $args) {
        $liste = \mywishlist\models\Liste::where('no', '=', $args['id'])->first();
        $items = \mywishlist\models\Item::where('liste_id', '=', $args['id'])->get();
        $this->view->render($response, 'liste.phtml', [
            "liste" => !is_null($liste) ? $liste : new \mywishlist\models\Liste(),
            "items" => !is_null($items) ? $items : array()
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