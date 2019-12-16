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
     * Permet de savoir si la liste a expiré
     *
     * @return bool
     * @throws \Exception
     */
    public function haveExpired() : bool {
        return new DateTime() > new DateTime($this->expiration);
    }
}