<?php
namespace mywishlist\controllers;

use DateTime;
use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\SetCookies;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\models\Item;
use mywishlist\models\Liste;
use mywishlist\models\Reservation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ItemController
 * @author Jules Sayer <jules.sayer@protonmail.com>
 * @author Anthony Pernot <anthony.pernot9@etu.univ-lorraine.fr>
 * @package mywishlist\controllers
 */
class ItemController extends Controller {

    /**
     * Appel item.phtml, permet d'afficher les informations
     * d'un item, l'état de sa réservation, et le nom stocké
     * en cookies de l'utilisateur
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getItem(Request $request, Response $response, array $args) : Response {
        try {
            $liste = Liste::where('token', '=', $args['token'])->firstOrFail();
            $item = Item::where(['id' => $args['id'], 'liste_id' => $liste->no])->firstOrFail();

            $cookies = Cookies::fromRequest($request);
            $haveCreated = $liste->haveCreated($request);
            $haveExpired = $liste->haveExpired();
            $canSee = $haveExpired || !$haveCreated;

            $this->view->render($response, 'item.phtml', [
                "liste" => $liste,
                "item" => $item,
                "reservation" => $item->reservation()->get(),
                "cookies" => $cookies,
                "canSee" => $canSee,
                "haveExpired" => $haveExpired
            ]);
        } catch(ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Cet objet n'existe pas...");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch(Exception $e) {
            $this->flash->addMessage('error', "Une erreur est survenue, veuillez réessayer ultérieurement.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    /**
     * Cette fonction permet de réserver un item
     * Elle vérifie que l'objet n'est pas déjà reservé
     * Que ce n'est pas le créateur qui réserve
     * et que la date d'expiration n'est pas dépassée
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function bookItem(Request $request, Response $response, array $args) : Response {
        try {
            $name = $request->getParsedBodyParam('name');
            $message = $request->getParsedBodyParam('message');
            $item_id = $request->getParsedBodyParam('item_id');
            $token = $request->getParsedBodyParam('token');

            $liste = Liste::where('token', '=', $token)->firstOrFail();
            $item = Item::where(['id' => $item_id, 'liste_id' => $liste->no])->firstOrFail();

            if($liste->haveCreated($request)) throw new Exception("Le créateur de la liste ne peut pas réserver d'objet.");
            if($liste->haveExpired()) throw new Exception("Cette liste a déjà expiré, il n'est plus possible de réserver des objets.");
            if(Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé.");


            $r = new Reservation();
            $r->item_id = $item_id;
            $r->message = $message;
            $r->nom = $name;
            $r->save();

            $nom = SetCookie::createRememberedForever('nom')->withValue($name);
            $response = SetCookies::fromResponse($response)->with($nom)->renderIntoSetCookieHeader($response);
            $this->flash->addMessage('success', "$name, votre réservation a été enregistrée !");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch(ModelNotFoundException $e) {
            $this->flash->addMessage('error', 'Nous n\'avons pas pu trouver cet objet.');
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}