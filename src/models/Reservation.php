<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model {
    public $timestamps = false;
    protected $table = "reservation";
    protected $primaryKey = "id";

    public function listes() {
        return $this->belongsTo('\mywishlist\models\Liste', 'no');
    }

    public function items() {
        return $this->belongsTo('\mywishlist\models\Item', 'id');
    }

}