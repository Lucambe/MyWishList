<?php
namespace mywishlist\models;
class Reservation extends \Illuminate\Database\Eloquent\Model {
    protected $table = "reservation";
    protected $primaryKey = "id";
    public $timestamps = false;
}