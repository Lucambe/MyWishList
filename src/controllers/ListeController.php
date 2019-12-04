<?php
namespace mywishlist\controllers;

class ListeController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function getListe($request, $response, $args) {
        try {
            $liste = \mywishlist\models\Liste::where('token', '=', $args['token'])->first();
            if(is_null($liste)) {
                throw new \Exception();
            }
            $items = \mywishlist\models\Item::where('liste_id', '=', $liste->no)->get();
            if(is_null($items)) {
                throw new \Exception();
            }
            $this->view->render($response, 'liste.phtml', [
                "liste" => $liste,
                "items" => $items
            ]);
        } catch(\Exception $e) {
            $response = $response->withRedirect($request->getUri()->getBaseUrl() . "/error/404" , 301);
        }
        return $response;
    }
}