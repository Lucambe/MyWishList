<?php

namespace mywishlist\models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Liste extends Model {
    public $timestamps = false;
    protected $table = "liste";
    protected $primaryKey = "no";

    public function items() {
        return $this->hasMany('\mywishlist\models\Item', 'liste_id');
    }

    public function messages() {
        return $this->hasMany('\mywishlist\models\Message', 'idListe');
    }

    public function reservations() {
        return $this->hasMany('\mywishlist\models\Reservation', 'liste_id');
    } 


    /**
     * Permet de savoir si la liste a expiré
     *
     * @return bool
     * @throws \Exception
     */
    public function haveExpired(): bool {
        return new DateTime() > new DateTime($this->expiration);
    }
}