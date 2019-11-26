<?php
namespace mywishlist\controllers;

use Slim\Http\Response;

class ItemController {

    protected $router;

    public function getItem($id) {
        $item = \mywishlist\models\Item::where('id','=',$id)->first();
        if($item === null) {
            $view = new \mywishlist\views\ItemView($item, "ITEM_404");
        } else {
            $view = new \mywishlist\views\ItemView($item, "ITEM_VIEW");
        }
        $template = new \mywishlist\views\TemplateView();
        $template->render($view->render());
    }

    public function getItems() {
        return \mywishlist\models\Item::get();
    }
}