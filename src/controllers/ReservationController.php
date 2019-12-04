<?php
namespace mywishlist\controllers;

class ReservationController{

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function reservItem($request, $response, $args){
        session_start();
        
        if(isset($_SESSION['name']) && isset($_SESSION['item'])){
            $nom = $_SESSION['name'];
            $numItem = $_SESSION['item'];
            $item = \mywishlist\models\Item::where('id','=',$numItem)->first();
            $liste = \mywishlist\models\Item::where('liste_id','=',$item)->get();
            if(!is_null($item) && !is_null($liste)){
                $reserv = new \mywishlist\models\Reservation();
                $reserv->nom = $nom;
                $reserv->item_id = $item;
                $reserv->liste_id = $liste;
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