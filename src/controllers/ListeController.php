<?php
namespace mywishlist\controllers;

use DateTime;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\models\Liste;
use mywishlist\models\Message;
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

            $created = is_object(json_decode(FigRequestCookies::get($request, 'created', '[]')->getValue())) ? json_decode(FigRequestCookies::get($request, 'created', '[]')->getValue()) : [];
            $infos = [
                "canSee" => $liste->haveExpired() || !in_array($liste->creationToken, $created),
                "haveExpired" => $liste->haveExpired(),
                "haveCreated" => in_array($liste->creationToken, $created)
            ];

            $this->view->render($response, 'liste.phtml', [
                "liste" => $liste,
                "items" => $liste->items()->get(),
                "reservations" => Reservation::get(),
                "messages" => $liste->messages()->get(),
                "nom" => FigRequestCookies::get($request, 'nom', '')->getValue(),
                "infos" => $infos
            ]);
        } catch(ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Cette liste n'existe pas...");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    /**
     * Appel updateliste.phtml, permet d'afficher le
     * formulaire de modification d'une liste
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getUpdateListe(Request $request, Response $response, array $args) : Response {
        try {
            $liste = Liste::where(['token' => $args['token'], 'creationToken' => $args['creationToken']])->firstOrFail();

            $this->view->render($response, 'updateliste.phtml', [
                "liste" => $liste
            ]);
        } catch(ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Token invalide.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    /**
     * Permet d'ajouter un message publique à une liste
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function addMessage(Request $request, Response $response, array $args) : Response {
        try {
            $name = $request->getParsedBody('name');
            $message = $request->getParsedBody('message');
            $token = $request->getParsedBody('token');
            if(!isset($name, $message, $token)) throw new Exception("Un des paramètres est manquant.");
            if(mb_strlen($message, 'utf8') < 10) throw new Exception("Votre message doit comporter au minimum 10 caractères.");
            if(mb_strlen($name, 'utf8') < 2) throw new Exception("Votre nom doit comporter au minimum 2 caractères.");

            $liste = Liste::where('token', '=', filter_var($token, FILTER_SANITIZE_STRING)->firstOrFail());

            $m = new Message();
            $m->idListe = $liste->no;
            $m->message = filter_var($message, FILTER_SANITIZE_STRING);
            $m->messager = filter_var($name, FILTER_SANITIZE_STRING);
            $m->save();

            $response = FigResponseCookies::set($response, SetCookie::create("nom")->withValue($name)->rememberForever());
            $this->flash->addMessage('success', "$name, Votre message a été envoyé");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
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
    public function createListe(Request $request, Response $response, array $args) : Response {
        try {
            $titre = $request->getParsedBodyParam('titre');
            $description = $request->getParsedBodyParam('descr');
            $dateExp = $request->getParsedBodyParam('dateExpi');
            if(!isset($titre, $description, $dateExp)) throw new Exception("Un des paramètres est manquant.");
            if(mb_strlen($titre, 'utf8') < 4) throw new Exception("Le titre de la liste doit comporter au minimum 4 caractères.");
            if(mb_strlen($description, 'utf8') < 10) throw new Exception("La description de la liste doit comporter au minimum 10 caractères.");
            if(new DateTime() > new DateTime($dateExp)) throw new Exception("La date d'expiration ne peut être déjà passée..");

            $titre = filter_var($titre, FILTER_SANITIZE_STRING);
            $description = filter_var($description, FILTER_SANITIZE_STRING);

            $liste = new Liste();
            $liste->user_id = 0;
            $liste->titre = $titre;
            $liste->description = $description;
            $liste->expiration = $dateExp;
            $liste->token = bin2hex(openssl_random_pseudo_bytes(32));
            $liste->creationToken = bin2hex(openssl_random_pseudo_bytes(12));
            $liste->validated = false;
            $liste->save();


            $created = is_object(json_decode(FigRequestCookies::get($request, 'created', '[]')->getValue())) ? json_decode(FigRequestCookies::get($request, 'created', '[]')->getValue()) : [];
            array_push($created, $liste->creationToken);
            $response = FigResponseCookies::set($response, SetCookie::create("created")->withValue(json_encode($created))->rememberForever());

            $this->flash->addMessage('success', "Votre liste a été créée!");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
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
            $createToken = $request->getParsedBodyParam('creationToken');

            if(!isset($titre, $description, $date, $token, $createToken)) throw new Exception("Un des paramètres est manquant.");
            if(mb_strlen($titre, 'utf8') < 4) throw new Exception("Le titre de la liste doit comporter au minimum 4 caractères.");
            if(mb_strlen($description, 'utf8') < 10) throw new Exception("La description de la liste doit comporter au minimum 10 caractères.");
            if(new DateTime() > new DateTime($date)) throw new Exception("La date d'expiration ne peut être déjà passée..");

            $liste = Liste::where(['token' => filter_var($token, FILTER_SANITIZE_STRING), 'creationToken' => filter_var($createToken, FILTER_SANITIZE_STRING)])->firstOrFail();

            $liste->titre = var_filter($titre, FILTER_SANITIZE_STRING);
            $liste->description = var_filter($description, FILTER_SANITIZE_STRING);
            $liste->expiration = var_filter($date, FILTER_SANITIZE_STRING);
            $liste->save();

            $this->flash->addMessage('success', "Votre modification a été enregistrée!");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage('error', "Impossible de modifier la liste.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}