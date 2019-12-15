<?php
namespace mywishlist\controllers;

use DateTime;
use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\SetCookies;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\models\Liste;
use mywishlist\models\Reservation;
use Slim\Http\Request;
use Slim\Http\Response;
use function mywishlist\models\Liste;

/**
 * Class ListeController
 * @author Jules Sayer <jules.sayer@protonmail.com>
 * @author Anthony Pernot <anthony.pernot9@etu.univ-lorraine.fr>
 * @package mywishlist\controllers
 */
class ListeController extends Controller {

    /**
     * Appel liste.phtml, permet d'afficher les informations
     * d'une liste, ses items, ses messages et l'état des réservations
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getListe(Request $request, Response $response, array $args) : Response {
        try {
            $liste = Liste::where('token', '=', $args['token'])->firstOrFail();

            $cookies = Cookies::fromRequest($request);
            $haveCreated = $cookies->has('created') ? in_array($liste->token, json_decode($cookies->get('created')->getValue())) : false;
            $haveExpired = new DateTime() > new DateTime($liste->expiration);
            $canSee = $haveExpired || !$haveCreated;
            $infos = [
                "canSee" => $canSee,
                "haveExpired" => $haveExpired,
                "haveCreated" => $haveCreated
            ];

            $this->view->render($response, 'liste.phtml', [
                "liste" => $liste,
                "items" => $liste->items()->get(),
                "reservations" => Reservation::get(),
                "messages" => $liste->messages()->get(),
                "cookies" => $cookies,
                "infos" => $infos
            ]);
        } catch(ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Cette liste n'existe pas...");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }


    /**
     * Créer une liste a partir d'une requête POST, retourne sur
     * l'accueil avec un flahs message, et ajoute le cookie de création
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function createListe(Request $request, Response $response, array $args): Response {
        try {
            $titre = $request->getParsedBodyParam('titre');
            $description = $request->getParsedBodyParam('descr');
            $dateExp = $request->getParsedBodyParam('dateExpi');
            $titre = filter_var($titre, FILTER_SANITIZE_STRING);
            $description = filter_var($description, FILTER_SANITIZE_STRING);

            $liste = new Liste();
            $liste->user_id = 0;
            $liste->titre = $titre;
            $liste->description = $description;
            $liste->expiration = $dateExp;
            $liste->token = bin2hex(openssl_random_pseudo_bytes(32));
            $liste->save();

            $created = Cookies::fromRequest($request)->has("created") ? json_decode(Cookies::fromRequest($request)->get("created")->getValue()) : [];
            array_push($created, $liste->token);
            $newCreated = SetCookie::createRememberedForever('created')->withValue(json_encode($created));
            $response = SetCookies::fromResponse($response)->with($newCreated)->renderIntoSetCookieHeader($response);

            $this->flash->addMessage('success', "Votre liste a été créée!");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', "Impossible de créer la liste.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    /**
     * Modifie la liste, vérifie que l'utilisateur a créé la liste
     * et qu'elle existe bel et bien.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateListe(Request $request, Response $response, array $args) : Response {
        try {
            $titre = $request->getParsedBodyParam('newTitle');
            $description = $request->getParsedBodyParam('newDescription');
            $date = $request->getParsedBodyParam('newDate');
            $token = $request->getParsedBodyParam('token');

            $created = Cookies::fromRequest($request)->has("created") ? json_decode(Cookies::fromRequest($request)->get("created")->getValue()) : [];
            if(!in_array($token, $created)) {
                throw new Exception();
            }

            $liste = Liste::where('token', '=', $token)->firstOrFail();
            $liste->titre = $titre;
            $liste->description = $description;
            $liste->expiration = $date;
            $liste->save();

            $this->flash->addMessage('success', "votre modification a été enregistrée !");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Impossible de modifier la liste.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', "Vous ne pouvez pas modifier cette liste.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}