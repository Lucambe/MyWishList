<?php


namespace mywishlist\controllers;


use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use mywishlist\models\Liste;
use mywishlist\models\Message;
use Slim\Http\Request;
use Slim\Http\Response;

class MessageController extends Controller {

    /**
     * @todo: Sécurité avec FILTER_VAR
     */
    public function addMessage(Request $request, Response $response, array $args) : Response {
        try {
            $name = $request->getParsedBody()['name'];
            $message = $request->getParsedBody()['message'];
            $token = $request->getParsedBody()['token'];
            $liste = Liste::where('token', '=', $token)->first();
            if(is_null($liste)) {
                throw new Exception();
            }

            $m = new Message();
            $m->idListe = $liste->no;
            $m->message = $message;
            $m->messager = $name;
            $m->save();
            $response = FigResponseCookies::set($response, SetCookie::create("nom")->withValue($name)->rememberForever());
            $this->flash->addMessage('success', "$name, Votre message a été envoyé");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch(Exception $e) {
            $this->flash->addMessage('error', 'Nous n\'avons pas pu envoyer votre message.');
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}