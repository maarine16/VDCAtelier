<?php

namespace Manager;

final class Application {
    private $controller; // position 3
    private $action; // position 4
    private $argument = ''; // position 5

    public function __construct() {
        $tab = explode('/', $_SERVER['REQUEST_URI']);
        // http://localhost:8888/boutiqueoo/web/membre/connexion
        //               0          1       2     3        4      (position dans le tableau)

        // http://localhost:8888/boutiqueoo/web/produit/fiche/45
        //               0          1       2     3       4    5  (position dans le tableau)

        if (!empty($tab[3]) && file_exists(__DIR__ . '/../../src/Controller/' . ucfirst($tab[3]) . 'Controller.php')) {
            $this->controller = 'Controller\\' . ucfirst($tab[3]) . 'Controller'; 
        } else {
            $this->controller = 'Controller\produitController';
        }

        // le paramètre à la position 4 correspond à la méthode 
        if (!empty($tab[4])) {
            $this->action = $tab[4];
        } else {
            $this->controller = 'Controller\produitController';
            $this->action = 'index';
        }
    
        // le paramètre à la position 5 correspond à l'argument passé à la méthode
        if (!empty($tab[5])) {
            $this->argument = urldecode($tab[5]); // urldecode : Remplace les '%20', '%....'
        }
    }

    public function run() {
        if (!is_null($this->controller)) {
            $a = new $this->controller;
            // ex : new Controller\MembreController 
        }

        if (!is_null($this->action) && method_exists($a, $this->action)) {
            // ex : produit/fiche/54 
            $action = $this->action;   // ex : fiche
            $a->$action($this->argument); // ex : $a->fiche(54)
        } else {
            require __DIR__ . '/../../src/View/404.html';
        }
    }


}