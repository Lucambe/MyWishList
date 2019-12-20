<?php

namespace mywishlist\controllers;

use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\SetCookies;
use Slim\Http\Response;
use Slim\Container;
use Slim\Http\Request;

abstract class CookiesController extends Controller {

    private $infos;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->infos = self::generateEmptyCookie();
    }

    private static function getCookies(Request $request): Cookies {
        return Cookies::fromRequest($request);
    }

    private static function generateEmptyCookie() {
        return [
            "name" => "",
            "creationTokens" => []
        ];
    }

    public function loadCookies(Request $request) {
        $cookies = self::getCookies($request);
        if ($cookies->has("wl_infos") && is_object(json_decode($cookies->get("wl_infos")))) {
            $arr = json_decode($cookies->get("wl_infos"));
            if (isset($arr['name']) && isset($arr['creationTokens']) && is_array($arr['creationTokens'])) {
                $this->infos = $arr;
            }
        }
    }


    public function createResponseCookie(Response $response) {
        return SetCookies::fromResponse($response)
            ->with(SetCookie::createRememberedForever("wl_infos")->withValue(json_encode($this->infos)))
            ->renderIntoSetCookieHeader($response);
    }

    public function getName() {
        return $this->infos['name'];
    }

    public function changeName(String $name) {
        $this->infos['name'] = $name;
    }

    public function getCreationTokens() {
        return $this->infos['creationTokens'];
    }

    public function addCreationToken(String $token) {
        if (!in_array($token, $this->infos['creationTokens'])) {
            array_push($this->infos['creationTokens'], $token);
        }
    }

    public function deleteCreationToken(String $token) {
        if (in_array($token, $this->infos['creationTokens'])) {
            unset($this->infos['creationTokens'][$token]);
        }
    }

}