<?php
namespace mywishlist\controllers;

class ListController {

    protected $app;
    protected $response;

    public function __construct($app, $response) {
        $this->app = $app;
        $this->response = $response;
    }

    public function getList($id) {
        $list = \mywishlist\models\Item::where('no','=',$id)->first();
        return $this->app->view->render($this->response, 'list.phtml', [
            "liste" => !is_null($list) ? $list : new \mywishlist\models\Liste()
        ]);
    }

    public function getLists() {
        $lists = \mywishlist\models\Item::get();
        return $this->app->view->render($this->response, 'lists.phtml', [
            "listes" => $lists
        ]);
    }
}