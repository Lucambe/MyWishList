<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    public $timestamps = false;
    protected $table = "message";
    protected $primaryKey = "id";

    public function liste() {
        return $this->belongsTo('\mywishlist\models\Liste', 'no');
    }
}