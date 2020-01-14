<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    public $timestamps = false;
    protected $table = "item";
    protected $primaryKey = "id";
    /**
     * Permet de mettre une image par défaut
     * @var array
     */
    protected $attributes = array('img' => "default.jpg");

    public function liste() {
        return $this->belongsTo('\mywishlist\models\Liste', 'no');
    }

    public function reservations() {
        return $this->hasOne('\mywishlist\models\Reservation', 'item_id');
    }
}