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
}