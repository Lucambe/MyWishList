<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Participe extends Model {
    public $timestamps = false;
    protected $table = "participe";
<<<<<<< HEAD
    protected $primaryKey = "id";
=======
    protected $primaryKey = "idPart";
>>>>>>> master

    public function cagnotte() {
        return $this->belongsTo('\mywishlist\models\Cagnotte', 'id');
    }
<<<<<<< HEAD
=======

    public function user() {
        return $this->hasOne('\mywishlist\models\User', 'id');
    }

>>>>>>> master
}