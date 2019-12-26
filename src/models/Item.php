<?php
namespace mywishlist\models;
use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    protected $table = "item";
    protected $primaryKey = "id";
    public $timestamps = false;

    /**
     * Permet de mettre une image par défaut
     * @var array
     */
    protected $attributes = array('img' => "default.jpg");

    public function liste() {
        return $this->belongsTo('\mywishlist\models\Liste', 'no');
    }

    public function reservation() {
        return $this->hasOne('\mywishlist\models\Reservation', 'item_id');
    }
}