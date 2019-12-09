<?php


namespace mywishlist\models;


class Message extends \Illuminate\Database\Eloquent\Model {
    protected $table = "message";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function liste() {
        return $this->belongsTo('\mywishlist\models\Liste', 'no');
    }
}