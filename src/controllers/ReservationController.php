<?php
namespace mywishlist\controllers;

class ReservationController{

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function reservItem($request, $response, $args){
       try{
           $this->creatCookies("name", "huberteuh");
            if(isset($_COOKIE['name']) ){//$this->getSessionName() != null){
                $nom = $_COOKIE['name'];//$this->getSessionName();
                $item = \mywishlist\models\Item::where('id','=',$args['id'])->first();
                $liste = \mywishlist\models\Item::where('liste_id','=',$item)->first();
                if(!is_null($item) && !is_null($liste)){
                    $reserv = new \mywishlist\models\Reservation();
                    $reserv->nom = $nom;
                    $reserv->item_id = $item;
                    $reserv->liste_id = $liste;
                    $reserv->message = "";
                    $reserv->save();
                   // echo "Effctué";
                    $this->view->render($response, 'reserver.phtml', [
                        "nom" => $reserv->nom,
                        "numero item" => $item
                    ]);
                }
            }
            
        }catch(\Exception $e){
            $response = $response->withRedirect($request->getUri()->getBaseUrl() . "/error/404" , 301);
        }
        return $response;
       } 


    public function creatCookies($name, $value){
        setcookie($name, $value, time() + 60*60*30);
    }

    /*public function getSessionName(){
        $nom=null;
        try{
            if(isset($_SESSION['name'])){
                $nom = $_SESSION['name'];
            }
        }catch(Exception $e){
            throw new Exception(<<<EOD
            La session n'existe pas 
            EOD
        );
        }  
        return $nom;
    }*/

}

?>