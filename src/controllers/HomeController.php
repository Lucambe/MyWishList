<?php
namespace mywishlist\controllers;

class HomeController extends Controller {

    public function showHome($request, $response, $args) {
        $this->view->render($response, 'home.phtml');
        return $response;
    }
}