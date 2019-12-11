<?php
namespace mywishlist\controllers;

use Dflydev\FigCookies\FigRequestCookies;
use Exception;
use mywishlist\models\Item;
use mywishlist\models\Liste;
use mywishlist\models\Reservation;

class ItemController extends Controller {

    public function getItem($request, $response, $args) {
        try {
            $liste = Liste::where('token', '=', $args['token'])->first();
            if(is_null($liste)) {
                throw new Exception();
            }
            $item = Item::where(['id' => $args['id'], 'liste_id' => $liste->no])->first();
            if(is_null($item)) {
                throw new Exception();
            }

            $this->view->render($response, 'item.phtml', [
                "liste" => $liste,
                "item" => $item,
                "reservation" => !is_null(Reservation::where('item_id', '=', $item->id)->first()),
                "nom" => urldecode(FigRequestCookies::get($request, 'nom', '')->getValue())
            ]);
        } catch (Exception $e) {
            $this->flash->addMessage('error', "Impossible de récupérer cet objet.");
            $response = $response->withRedirect($this->router->pathFor('home'));
        }
        return $response;
    }
}