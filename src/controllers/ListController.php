<?php
namespace mywishlist\controllers;

class ListController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function getList($request, $response, $args) {
        $list = \mywishlist\models\Item::where('no','=',$args['id'])->first();

        $this->view->render($response, 'list.phtml', [
            "liste" => !is_null($list) ? $list : new \mywishlist\models\Liste()
        ]);
        return $response;
    }

    public function getLists($request, $response, $args) {
        $lists = \mywishlist\models\Item::get();
        $this->view->render($response, 'lists.phtml', [
            "listes" => $lists
        ]);
        return $response;
    }
}