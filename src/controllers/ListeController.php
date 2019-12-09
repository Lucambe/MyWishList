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
            $items = $liste->items()->get();
            $messages = $liste->messages()->get();
            if(is_null($items)||is_null($messages)) {
                throw new \Exception();
            }
            $this->view->render($response, 'liste.phtml', [
                "liste" => $liste,
                "items" => $items,
                "messages" => $messages
            ]);
        } catch(\Exception $e) {
            $response = $response->withRedirect($request->getUri()->getBaseUrl() . "/error/404" , 301);
        }
        return $response;
    }


}