<?php
require('vendor/autoload.php');
use Illuminate\Database\Capsule\Manager as DB;
use mywishlist\models\Liste as Liste;
use mywishlist\models\Item as Item;
$db = new DB();
$db->addConnection(parse_ini_file("src/config/database.ini"));
$db->setAsGlobal();
$db->bootEloquent();


// Lister les listes de souhaits
$listes = Liste::get();
foreach($listes as $liste) {
    echo $liste->no . ": " . $liste->titre . " - " . $liste->description . "<br/>";
}

echo "<br/><br/>";

// Lister les items
$items = Item::get();
foreach($items as $item) {
    echo $item->id . ": " . $item->nom . " - " . $item->descr . "<br/>";
}

echo "<br/><br/>";

// Afficher un item en particulier, dont l'id est passé en paramêtre dans l'url (test.php?id=1)
if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    $i = Item::where('id', '=', $_GET['id'])->first();
    if($i != null) {
        echo $i->id . ": " . $i->nom . " - " . $i->descr . "<br/>";
    } else {
        echo "L'id n'existe pas";
    }
}

echo "<br/><br/>";

// Créer un item, l'insérer dans la base et l'ajouter à une liste de souhaits.

$i2 = new Item();
$i2->liste_id = 5;
$i2->nom = "Test";
$i2->descr = "Test";
$i2->img = "undostres.jpg";
$i2->tarif = 667.69;
if($i2->save()) {
    echo "Ajout OK";
}