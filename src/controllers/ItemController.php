<?php
namespace mywishlist\controllers;

class ItemController {

    protected $app;
    protected $response;

    public function __construct($app, $response) {
        $this->app = $app;
        $this->response = $response;
    }

    public function getItem($id) {
        $item = \mywishlist\models\Item::where('id','=',$id)->first();
        return $this->app->view->render($this->response, 'item.tpl', [
            "item" => !is_null($item) ? $item : new \mywishlist\models\Item()
        ]);
    }

    public function getItems() {
        $items = \mywishlist\models\Item::get();
        return $this->app->view->render($this->response, 'items.tpl', [
            "items" => $items
        ]);
    }
}