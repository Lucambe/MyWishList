<?php


namespace mywishlist\controllers;


class AboutController
{
    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function showAbout($request, $response, $args) {
        $this->view->render($response, 'about.phtml');
        return $response;
    }
}