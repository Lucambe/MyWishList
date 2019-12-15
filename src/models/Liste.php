<?php

namespace mywishlist\models;

use DateTime;
use Dflydev\FigCookies\Cookies;
use Illuminate\Database\Eloquent\Model;
use Slim\Http\Request;

class Liste extends Model{
    protected $table = "liste";
    protected $primaryKey = "no";
    public $timestamps = false;

    public function items() {
        return $this->hasMany('\mywishlist\models\Item', 'liste_id');
    }

    public function messages() {
        return $this->hasMany('\mywishlist\models\Message', 'idListe');
    }

    /**
     * Cette fonction récupère un tableau à partir
     * du cookie "created", si le cookie n'existe pas
     * elle renvoie un tableau vide.
     *
     * @param Request $request
     * @return array
     */
    public function getCreationTokens(Request $request) : array {
        if(Cookies::fromRequest($request)->has('created')) {
            return json_decode(Cookies::fromRequest($request)->get('created')->getValue());
        } else {
            return [];
        }
    }

    /**
     * Permet de savoir si l'utilisateur qui
     * a envoyé la requête est le créateur
     *
     * @param Request $request
     * @return bool
     */
    public function haveCreated(Request $request) : bool {
        $tokens = $this->getCreationTokens($request);
        return in_array($this->token, $tokens);
    }

    /**
     * Permet de savoir si la liste a expiré
     *
     * @return bool
     * @throws \Exception
     */
    public function haveExpired() : bool {
        return new DateTime() > new DateTime($this->expiration);
    }
}