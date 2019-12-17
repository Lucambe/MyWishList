<?php
namespace mywishlist\controllers;

use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\SetCookie;
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

            $created = is_object(json_decode(FigRequestCookies::get($request, 'created', '[]')->getValue())) ? json_decode(FigRequestCookies::get($request, 'created', '[]')->getValue()) : [];
            $infos = [
                "canSee" => $liste->haveExpired() || !in_array($liste->tokenCreation, $created),
                "haveExpired" => $liste->haveExpired(),
                "haveCreated" => in_array($liste->tokenCreation, $created)
            ];

            $this->view->render($response, 'item.phtml', [
                "liste" => $liste,
                "item" => $item,
                "reservation" => $item->reservation()->get(),
                "nom" => FigRequestCookies::get($request, 'nom', '')->getValue(),
                "infos" => $infos
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

    public function getCreateItem(Request $request, Response $response, array $args) : Response {
        try {
            $liste = Liste::where(['token' => $args['token'], 'creationToken' => $args['creationToken']])->firstOrFail();

            $this->view->render($response, 'createitem.phtml', [
                "liste" => $liste
            ]);
        } catch(ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Token invalide.");
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
            $name = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $message = filter_var($request->getParsedBodyParam('message'), FILTER_SANITIZE_STRING);
            $item_id = filter_var($request->getParsedBodyParam('item_id'), FILTER_SANITIZE_NUMBER_INT);
            $token = filter_var($request->getParsedBodyParam('token'), FILTER_SANITIZE_STRING);

            if(!isset($name, $message, $item_id, $token)) throw new Exception("Un des paramètres est manquant.");

            $liste = Liste::where('token', '=', $token)->firstOrFail();
            $item = Item::where(['id' => $item_id, 'liste_id' => $liste->no])->firstOrFail();

            $created = is_object(json_decode(FigRequestCookies::get($request, 'created', '[]')->getValue())) ? json_decode(FigRequestCookies::get($request, 'created', '[]')->getValue()) : [];
            if(in_array($liste->token, $created)) throw new Exception("Le créateur de la liste ne peut pas réserver d'objet.");
            if($liste->haveExpired()) throw new Exception("Cette liste a déjà expiré, il n'est plus possible de réserver des objets.");
            if(Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé.");


            $r = new Reservation();
            $r->item_id = $item_id;
            $r->message = $message;
            $r->nom = $name;
            $r->save();

            $response = FigResponseCookies::set($response, SetCookie::create("nom")->withValue($name)->rememberForever());
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

    public function createItem(Request $request, Response $response, array $args) : Response {
        try{

            $nom = filter_var($request->getParsedBodyParam('nom'), FILTER_SANITIZE_STRING);
            $description = filter_var($request->getParsedBodyParam('descr'), FILTER_SANITIZE_STRING);
            $file = $request->getParsedBodyParam('file');
            $url = filter_var($request->getParsedBodyParam('url'), FILTER_SANITIZE_URL);
            $prix = filter_var($request->getParsedBodyParam('prix'), FILTER_SANITIZE_NUMBER_INT);
            $token = filter_var($request->getParsedBodyParam('token'), FILTER_SANITIZE_STRING);
            $createToken = filter_var($request->getParsedBodyParam('creationToken'), FILTER_SANITIZE_STRING);

            if(!isset($nom, $description, $file, $prix, $token, $createToken)) throw new Exception("Un des paramètres est manquant.");

            $i = Liste::where(['token' => $token, 'creationToken' => $createToken])->firstOrFail();

            $item = new Item();
            $item->liste_id = $i->no;
            $item->nom = $nom;
            $item->descr=$description;
            $item->img=$file;
            $item->url=$url;
            $item->tarif=$prix;
            $item->save();

            $this->flash->addMessage('success', "Votre item a été enregistrée !");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(ModelNotFoundException $e){
            $this->flash->addMessage('error', 'Nous n\'avons pas pu créer cet item.');
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(Exception $e){
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

}