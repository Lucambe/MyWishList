<?php
namespace mywishlist\controllers;

class ErrorController {

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function showError($request, $response, $args) {
        $this->view->render($response, 'error.phtml', [
            "ERROR_CODE" => $args['n']
        ]);
        return $response;
    }
}