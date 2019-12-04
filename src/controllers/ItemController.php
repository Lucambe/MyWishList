<?php
namespace mywishlist\controllers;

class ItemController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function getItem($request, $response, $args) {
        try {
            $liste = \mywishlist\models\Liste::where('token', '=', $args['token'])->first();
            if(is_null($liste)) {
                throw new \Exception();
            }
            $item = \mywishlist\models\Item::where(['id' => $args['id'], 'liste_id' => $liste->no])->first();
            if(is_null($item)) {
                throw new \Exception();
            }
            $this->view->render($response, 'item.phtml', [
                "liste" => $liste,
                "item" => $item
            ]);
        } catch (\Exception $e) {
            $response = $response->withRedirect($request->getUri()->getBaseUrl() . "/error/404" , 301);
        }

        return $response;
    }
}