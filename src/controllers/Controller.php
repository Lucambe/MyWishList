<?php
namespace mywishlist\controllers;

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

    public function __construct($container) {
        $this->flash = $container->flash;
        $this->router = $container->router;
        $this->view = $container->view;
    }
}