<?php
namespace mywishlist\models;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    protected $table = "message";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function liste() {
        return $this->belongsTo('\mywishlist\models\Liste', 'no');
    }
}