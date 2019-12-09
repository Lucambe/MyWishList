<?php
namespace mywishlist\controllers;

class ItemController extends Controller {

    public function getItem($request, $response, $args) {
        try {
            $liste = \mywishlist\models\Liste::where('token', '=', $args['token'])->first();
            if(is_null($liste)) {
                throw new \Exception();
            }
            $item = \mywishlist\models\Item::where(['id' => $args['id'], 'liste_id' => $liste->no])->first();
            if(is_null($item)) {
                throw new \Exception();
            }

            $this->view->render($response, 'item.phtml', [
                "liste" => $liste,
                "item" => $item,
                "reservation" => !is_null(\mywishlist\models\Reservation::where('item_id', '=', $item->id)->first()),
                "nom" => is_null(\Dflydev\FigCookies\FigRequestCookies::get($request, 'nom')) ? "" : explode("=", \Dflydev\FigCookies\FigRequestCookies::get($request, 'nom'))[1]
            ]);
        } catch (\Exception $e) {
            $response = $response->withRedirect($request->getUri()->getBaseUrl() . "/error/404" , 301);
        }
        return $response;
    }
}