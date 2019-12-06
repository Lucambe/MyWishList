<?php
namespace mywishlist\controllers;

class ReservationController{

    protected $view;

    public function __construct($viewRenderer) {
        $this->view = $viewRenderer;
    }

    public function reservItem($request, $response, $args){
       try{
           if(isset($_POST['valid_freserv']) && $_POST['valid_freserv']=='valid_f1'){
           $n = $_POST['nom']; $i = $_POST['numItem'];
            $this->creatCookies("name", $n);
            if(isset($_COOKIE['name']) ){
                $nom = $_COOKIE['name'];
                $item = \mywishlist\models\Item::where('id','=',$args['id'])->first();
                if(!is_null($item)){ 
                    $reserv = new \mywishlist\models\Reservation();
                    $reserv->nom = $nom;
                    $reserv->item_id = $item->id;
                    $reserv->liste_id = $item->liste_id;
                    $reserv->message = "";
                    $reserv->save();
                    $this->view->render($response, 'reserverItem.phtml', [
                        "nom" => $reserv->nom,
                        "numero item" => $item
                    ]);
                }
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

    public function getSessionName(){
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
    }

}

?>