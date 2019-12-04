<?php
namespace mywishlist\controllers;

class ListeController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function getListe($request, $response, $args) {
        $liste = \mywishlist\models\Liste::where('token', '=', $args['token'])->first();
        $items = \mywishlist\models\Item::where('liste_id', '=', $liste->no)->get();
        if(!is_null($liste) && !is_null($items)) {
            $this->view->render($response, 'liste.phtml', [
                "liste" => $liste,
                "items" => $items
            ]);
        } else {
            $response = $response->withRedirect($request->getUri()->getBaseUrl() . "/error/404" , 301);
        }
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