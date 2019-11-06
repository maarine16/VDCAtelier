<?php  

namespace Manager; // On nomme le namespace dans lequel on se trouve

use PDO; // On appel la Class PDO de php
use Config; // On appel la Class Config de php

class PDOManager {
    private static $instance = null;
    private function __construct() {}
    private function __clone() {}
    public static function getInstance() {
        if (is_null(self::$instance)) { // si le parametre instance de l'objet est null
            self::$instance = new self; // alors on le crée 
        }
        return self::$instance;
    }
    public function getPdo() {
        require_once __DIR__ . '/../../app/Config.php'; // Accès au fichier Config.php et Config/parameters.php 
        $config = new Config;
        $connect = $config->getParametersConnect();
        // On instancie un objet de la classe config qui a pour mission de nous transmettre les infos de connexion
        return new PDO(
            // Les informations qui suivent, se retrouvent dans Config/parameters.php 
            'mysql:host=' . $connect['host'] . ';dbname=' . $connect['dbname'],
            $connect['login'],
            $connect['password'],
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            )
        );
    }
}