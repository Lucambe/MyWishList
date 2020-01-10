<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    public $timestamps = false;
    protected $table = "user";
    protected $primaryKey = "id";
}