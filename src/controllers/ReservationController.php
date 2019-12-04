<?php
namespace mywishlist\controllers;

class ReservationController{

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function reservItem($request, $response, $args){
        session_start();
        
        if(isset($_SESSION['name'])){
            $nom = $_SESSION['name'];
            $item = \mywishlist\models\Item::where('id','=',$args['id'])->first();
            $liste = \mywishlist\models\Item::where('liste_id','=',$item)->get();
            if(!is_null($item) && !is_null($liste)){
                $reserv = new \mywishlist\models\Reservation();
                $reserv->nom = $nom;
                $reserv->item_id = $item;
                $reserv->liste_id = $liste;
                $reserv->save();
                $this->view->render($response, 'reserver.phtml', [
                    "nom" => $nom,
                    "numero item" => $item
                ]);
            }
        }else{
            $response = $response->withRedirect($request->getUri()->getBaseUrl() . "/error/404" , 301);
        }
        session_destroy();
       return $response;
    }

}

?>