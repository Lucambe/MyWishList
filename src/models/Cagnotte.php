<?php


namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;
class Cagnotte extends Model
{
    public $timestamps = false;
    protected $table = "cagnotte";
    protected $primaryKey = "id";

    public function is_full(){
        return $this->recolte < $this->montant;
    }

    public function participe() {
        return $this->hasOne('\mywishlist\models\Participe', 'id_cagnotte');
    }

    public function item() {
        return $this->hasOne('\mywishlist\models\Item', 'id_cagnotte');
    }

}