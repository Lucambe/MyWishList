<?php
namespace mywishlist\controllers;

class ItemController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function getItem($request, $response, $args) {
        $item = \mywishlist\models\Item::where('id','=',$args['id'])->first();

        $this->view->render($response, 'item.phtml', [
            "item" => !is_null($item) ? $item : new \mywishlist\models\Item()
        ]);
        return $response;
    }

    public function getItems($request, $response, $args) {
        $items = \mywishlist\models\Item::get();
        $this->view->render($response, 'items.phtml', [
            "items" => $items
        ]);
        return $response;
    }
}