<?php
namespace mywishlist\controllers;

class ItemController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function getItem($request, $response, $args) {
        $item = \mywishlist\models\Item::where('id','=',$args['id'])->first();
        if(!is_null($item)) {
            $this->view->render($response, 'item.phtml', [
                "item" => $item
            ]);
        } else {
            $response = $response->withRedirect($request->getUri()->getBaseUrl() . "/error/404" , 301);
        }
        return $response;
    }
}