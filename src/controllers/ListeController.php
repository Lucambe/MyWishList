<?php
namespace mywishlist\controllers;

use Exception;
use mywishlist\models\Liste;
use mywishlist\models\Reservation;
use function mywishlist\models\Liste;

class ListeController extends Controller {

    public function getListe($request, $response, $args) {
        try {
            $liste = Liste::where('token', '=', $args['token'])->first();
            if(is_null($liste)) {
                throw new Exception();
            }
            $items = $liste->items()->get();
            $messages = $liste->messages()->get();
            if(is_null($items) || is_null($messages)) {
                throw new Exception();
            }
            foreach ($items as $i) {
                $reservations[$i->id] = !is_null(Reservation::where('item_id', '=', $i->id)->first());
            }
            $this->view->render($response, 'liste.phtml', [
                "liste" => $liste,
                "items" => $items,
                "messages" => $messages,
                "reservations" => $reservations
            ]);
        } catch(Exception $e) {
            $this->flash->addMessage('error', "Cette liste n'existe pas");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }


    public function createListe($request, $response, $args){
             try{
                $titre = $request->getParsedBody()['titre'];      
                $description = $request->getParsedBody()['descr'];    
                $dateExp = $request->getParsedBody()['dateExpi'];      
                $idUser = $request->getParsedBody()['id'];
                    if( ! filter_var($idUser, FILTER_VALIDATE_INT)  ){
                        $this->flash->addMessage('error', "Votre enregistrement a échoué, veuillez réessayer.");
                        $response = $response->withRedirect($this->router->pathFor('home'));
                    }else{

                        $titre = filter_var($titre, FILTER_SANITIZE_STRING);
                        $description = filter_var($description, FILTER_SANITIZE_STRING);

                        $liste = new Liste();
                       // $searchIdUser = $liste::where('user_id','=', $idUser)->first();
                        $nb = $liste::count('no');
                        $liste->no = $nb+1;
                        $liste->user_id = $idUser;
                        $liste->titre = $titre;
                        $liste->description = $description;
                        $liste->expiration = $dateExp;
                        $liste->token = "nosecure".($nb+1);
                        
                        /*if(is_null($searchIdUser)){
                            $searchIdUser = $liste::select('user_id')->get() + 1;
                            $liste->user_id = $searchIdUser;
                        }else{
                            $liste->user_id = $searchIdUser;
                        }*/
                      
                        $liste->save();
                        $this->flash->addMessage('success', "votre réservation a été enregistrée !");
                        $response = $response->withRedirect($this->router->pathFor('home'));
                    }
            } catch(Exception $e){
                $this->flash->addMessage('error', "Impossible de créer la liste.");
                $response = $response->withRedirect($this->router->pathFor('home'));
            }
        return $response;
    }

    public function updateListe($request,$response, $args){
        try{
            $titre = $request->getParsedBody()['newTitle'];
            $description = $request->getParsedBody()['newDescription'];
            $date = $request->getParsedBody()['newDate'];
            $token = $request->getParsedBody()['tokenFound'];

            $liste  = Liste::where('token','=',$token)->first();
            $liste->titre = $titre;
            $liste->description = $description;
            $liste->expiration = $date;
          
            $liste->save();
            $this->flash->addMessage('success', "votre modification a été enregistrée !");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }catch(Exception $e){
            $this->flash->addMessage('error', "Impossible de modifier la liste.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }


        return $response;
    }

    

}