<?php
namespace mywishlist\controllers;

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
class ItemController extends CookiesController {

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
            $this->loadCookiesFromRequest($request);

            $can = [
                "canSee" => $liste->haveExpired() || !in_array($liste->creationToken, $this->getCreationTokens()),
                "haveExpired" => $liste->haveExpired(),
                "haveCreated" => in_array($liste->tokenCreation, $this->getCreationTokens())
            ];

            $this->view->render($response, 'item.phtml', [
                "liste" => $liste,
                "item" => $item,
                "reservation" => $item->reservation()->get(),
                "nom" => $this->getName(),
                "infos" => $can
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
            $name = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $message = filter_var($request->getParsedBodyParam('message'), FILTER_SANITIZE_STRING);
            $item_id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);
            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);

            if(mb_strlen($name, 'utf8') < 2) throw new Exception("Votre nom doit comporter au minimum 2 caractères");

            $liste = Liste::where('token', '=', $token)->firstOrFail();
            $item = Item::where(['id' => $item_id, 'liste_id' => $liste->no])->firstOrFail();

            if(in_array($liste->token, $this->getCreationTokens())) throw new Exception("Le créateur de la liste ne peut pas réserver d'objet.");
            if($liste->haveExpired()) throw new Exception("Cette liste a déjà expiré, il n'est plus possible de réserver des objets.");
            if(Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé.");


            $r = new Reservation();
            $r->item_id = $item_id;
            $r->message = $message;
            $r->nom = $name;
            $r->save();

            $this->changeName($name);
            $response = $this->createResponseCookie($response);
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
        try {
            $nom = filter_var($request->getParsedBodyParam('nom'), FILTER_SANITIZE_STRING);
            $description = filter_var($request->getParsedBodyParam('descr'), FILTER_SANITIZE_STRING);
            $file = $request->getUploadedFiles('file');
            $url = filter_var($request->getParsedBodyParam('url'), FILTER_SANITIZE_URL);
            $prix = filter_var($request->getParsedBodyParam('prix'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);
            $creationToken = filter_var($args['creationToken'], FILTER_SANITIZE_STRING);

            if(mb_strlen($nom, 'utf8') < 2) throw new Exception("Le nom doit comporter au minimum 2 caractères");
            if(mb_strlen($prix, 'utf8') < 1) throw new Exception("Le prix doit comporter au moins 1 caractère");

            /**
             * WTF Anthony, si coté client la valeur est modifiée, il peut upload un fichier dont la taille peut être choisie...
             * @todo !!!
             */
            if($file['size'] > 2097152) throw new Exception("La taille de l'image est trop grande");

            $i = Liste::where(['token' => $token, 'creationToken' => $creationToken])->firstOrFail();

            $item = new Item();
            $item->liste_id = $i->no;
            $item->nom = $nom;
            $item->descr = $description;

            /* Si pas d'image uploadé, ne rien faire, car le modèle Item
            met déjà une image par défaut (default.png), l'upload d'image n'est pas encore gérée correctement, la ligne sera donc à décommenter quand ça sera fait,
            et il faudra tester si une image a été upload avant de la set.
            */
            // $item->img = $file['file']['name'];
            $item->url = $url;
            $item->tarif = $prix;
            $item->save();

            move_uploaded_file($file['file']['tmp_name'], '/public/images/'.$file['file']['name']);

            $this->flash->addMessage('success', "Votre item a été enregistrée !");
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }catch(ModelNotFoundException $e){
            $this->flash->addMessage('error', 'Nous n\'avons pas pu créer cet item.');
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }catch(Exception $e){
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }
        return $response;
    }

    /**
     * @todo: Supprimer l'image uploadée associé à l'item
     */
    public function deleteItem(Request $request, Response $response, array $args ) : Response {
        try {
            $token = filter_var($args['token'],FILTER_SANITIZE_STRING);
            $creationToken = filter_var($args['creationToken'], FILTER_SANITIZE_STRING);
            $item_id  = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);
            
            $liste = Liste::where(['token' =>  $token, 'creationToken' => $creationToken])->firstOrFail();
            $item = Item::where(['id' => $item_id, 'liste_id' => $liste->no])->firstOrFail();
            if(Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé, il est donc impossible de le supprimer.");

            $item->destroy($item_id);

            $this->flash->addMessage('success', "Votre objet a été supprimé !");
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }catch(ModelNotFoundException $e){
            $this->flash->addMessage('error', 'Nous n\'avons pas pu supprimer cet item.');
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }catch(Exception $e){
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }
        return $response;
    }

    /**
     * @todo: ajouter modification url et image!
     */
    public function updateItem(Request $request, Response $response, array $args) : Response {
        try {
            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);
            $creationToken = filter_var($args['creationToken'], FILTER_SANITIZE_STRING);
            $item_id = filter_var($args['id'], FILTER_SANITIZE_STRING);
            $nom = filter_var($request->getParsedBodyParam('name'), FILTER_SANITIZE_STRING);
            $description = filter_var($request->getParsedBodyParam('desc'), FILTER_SANITIZE_STRING);
            $prix = filter_var($request->getParsedBodyParam('prix'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
            if(mb_strlen($prix, 'utf8') < 2) throw new Exception("Le nom doit comporter au moins 1 caractère");
            if(mb_strlen($prix, 'utf8') < 1) throw new Exception("Le prix doit comporter au moins 1 caractère");

            $liste = Liste::where(['token' => $token, 'creationToken' => $creationToken])->firstOrFail();
            $item = Item::where(['id' => $item_id, 'liste_id' => $liste->no])->firstOrFail();
            if(Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé, il ne peut donc pas être modifié.");

            $item->nom = $nom;
            $item->descr = $description;
            $item->tarif = $prix;
            $item->save();

            $this->flash->addMessage('success', "Votre item a été modifié !");
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }catch(ModelNotFoundException $e){
            $this->flash->addMessage('error', 'Nous n\'avons pas pu modifier cet item.');
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }catch(Exception $e){
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('showAdminListe', ['token' => $token, 'creationToken' => $creationToken]));
        }
        return $response;
    }

    /**
    *    @todo : FONCTIONS SUIVANTES PAS FINI NI FIXER
    */
    public function addImgItem(Request $request, Response $response, array $args) : Response {
        try{

            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);
            $creationToken = filter_var($args['creationToken'], FILTER_SANITIZE_STRING);
            $item_id = filter_var($args['id'], FILTER_SANITIZE_STRING);
            $url = filter_var($request->getParsedBodyParam('picture'),FILTER_SANITIZE_URL);

            if(!isset($token, $creationToken, $item_id, $url)){
                throw new Exception("Vous n'avez pas spécifié votre token de créateur ou le cookie du créateur n'existe pas tout simplement, 
                cela veut dire que vous n'êtes pas le propriétaire de la liste. De plus, êtes-vous sûr d'avoir spécifié le lien de l'image à ajouter ?");
            }
            
                $liste = Liste::where(['token' =>  $token, 'creationToken' => $createToken])->firstOrFail();
                $item = Item::where(['liste_id' => $liste->no , 'id' => $item_id])->firstOrFail();
                if(Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé, il ne peut donc pas être modifié.");

                $item->img=$url;
                $item->save();

                $this->flash->addMessage('success', 'Une image a été ajouter à votre item !');
                $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(ModelNotFoundException $e){
            $this->flash->addMessage('error', 'Nous n\'avons pas pu ajouter d\'image à cet item.');
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(Exception $e){
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }

        return $response;
    }

    
    public function editImgItem(Request $request, Response $response, array $args) : Response {
        try{

            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);
            $creationToken = filter_var($args['creationToken'], FILTER_SANITIZE_STRING);
            $item_id = filter_var($args['id'], FILTER_SANITIZE_STRING);
            $url = filter_var($request->getParsedBodyParam('picture'),FILTER_SANITIZE_URL);

            if(!isset($token, $creationToken, $item_id)){
                throw new Exception("Vous n'avez pas spécifié votre token de créateur ou le cookie du créateur n'existe pas tout simplement, 
                cela veut dire que vous n'êtes pas le propriétaire de la liste. ");
            }

            $liste = Liste::where(['token' => $token, 'creationToken' => $creationToken])->firstOrFail();
            $item = Item::where(['id' => $item_id, 'liste_id' => $liste->no])->firstOrFail();
            if(Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé, il ne peut donc pas être modifié.");

            $item->img = $url;
            $item->save();

            $this->flash->addMessage('success', 'L\'image de votre item a été modifier !');
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(ModelNotFoundException $e){
            $this->flash->addMessage('error', 'Nous n\'avons pas pu supprimer l\'image de cet item.');
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(Exception $e){
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

    public function deleteImgItem(Request $request, Response $response, array $args) : Response {
        try{
            $token = filter_var($args['token'], FILTER_SANITIZE_STRING);
            $creationToken = filter_var($args['creationToken'], FILTER_SANITIZE_STRING);
            $item_id = filter_var($args['id'], FILTER_SANITIZE_STRING);

            if(!isset($token, $creationToken, $item_id)){
                throw new Exception("Vous n'avez pas spécifié votre token de créateur ou le cookie du créateur n'existe pas tout simplement, 
                cela veut dire que vous n'êtes pas le propriétaire de la liste.");
            }

            $liste = Liste::where(['token' =>  $token, 'creationToken' => $createToken])->firstOrFail();
            $item = Item::where(['liste_id' => $liste->no , 'id' => $item_id])->firstOrFail();
            if(Reservation::where('item_id', '=', $item_id)->exists()) throw new Exception("Cet objet est déjà reservé, il ne peut donc pas être modifié.");
            if(!isset($item->img)) throw new Exception("Cet item ne possède pas d'image.");

            $item->img->delete();

            $this->flash->addMessage('success', "L'image de votre item à été supprimer !");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(ModelNotFoundException $e){
            $this->flash->addMessage('error', 'Nous n\'avons pas pu supprimer l\'image de cet item.');
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(Exception $e){
            $this->flash->addMessage('error', $e->getMessage());
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }

}