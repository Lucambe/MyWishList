<?php
namespace mywishlist\config;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;

class Database {

    public static function connect(){
        if(file_exists('src/config/database.ini')) {
            $data = parse_ini_file('src/config/database.ini');
        } else {
            throw new Exception("Fichier src/config/database.ini manquant");
        }

        $db = new DB();
        $db->addConnection( [
            'driver'    => $data['driver'],
            'host'      => $data['host'],
            'database'  => $data['database'],
            'username'  => $data['username'],
            'password'  => $data['password'],
            'charset'   => $data['charset'],
            'collation' => $data['collation'],
            'prefix'    => ''
        ]);
        $db->setAsGlobal();
        $db->bootEloquent();
    }
}