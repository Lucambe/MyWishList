<?php
namespace mywishlist\controllers;
class ItemController {

    public function getItem($id) {
        $item = \mywishlist\models\Item::where('id','=',$id)->first();
        if($item === null) {
            $view = new \mywishlist\views\ItemView($item, "ITEM_404");
        } else {
            $view = new \mywishlist\views\ItemView($item, "ITEM_VIEW");
        }
        $view->render();
    }

    public function getItems() {
        $item = \mywishlist\models\Item::get();
        $view = new \mywishlist\views\ItemView($item, "LIST_VIEW");
        $view->render();
    }
}