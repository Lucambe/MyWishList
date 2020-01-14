<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Participe extends Model {
    public $timestamps = false;
    protected $table = "participe";
    protected $primaryKey = "id";

    public function cagnotte() {
        return $this->belongsTo('\mywishlist\models\Cagnotte', 'id');
    }
}