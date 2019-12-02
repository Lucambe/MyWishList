<?php
namespace mywishlist\controllers;

class HomeController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function showHome($request, $response, $args) {
        $this->view->render($response, 'home.phtml');
        return $response;
    }
}