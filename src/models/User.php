<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    public $timestamps = false;
    protected $table = "user";
    protected $primaryKey = "id";

    public function liste() {
        return $this->hasOne('\mywishlist\models\Liste', 'user_id');
    }

    public function participes() {
        return $this->hasOne('\mywishlist\models\Participe', 'id_user');
    }

}

