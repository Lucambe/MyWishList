<?php

namespace mywishlist\controllers;

use Slim\Container;

/**
 * Class Controller
 * @author Jules Sayer <jules.sayer@protonmail.com>
 * @abstract
 * @package mywishlist\controllers
 */
abstract class Controller {
    protected $view;
    protected $router;
    protected $flash;

    /**
     * Controller constructor.
     * @param $container
     */
    public function __construct(Container $container) {
        $this->flash = $container->flash;
        $this->router = $container->router;
        $this->view = $container->view;
    }
}