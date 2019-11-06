<?php 

namespace Controller; 

use Config, Model;

class Controller {
    protected $model;
    protected $url; 

    public function __construct() {
        // lors d'un nouvel objet Controller, un objet Model sera créer également
        // exemple :
        // en entrée : Controller\produitController 
        // instanciation de Model\produitModel

        $classModel = str_replace('Controller', 'Model', get_called_class());
        $this->model = new $classModel;
        $config = new Config;
        $this->url = $config->getParametersUri();
    }

    public function getModel() {
        return $this->model;
    }

    public function getOtherModel($param) {

        $other_model = __DIR__ . '/../../src/Model/' .$param .'.php';
        var_dump($other_model);
        return $other_model;
    }

    public function redirect($adresse) {
        header('location:' . $adresse);
        exit();
    }

    public function render($layout, $view, $params) {
        $dirView = __DIR__ . '/../../src/View/';
        var_dump($dirView);

        $dirFile = $this->getModel()->getTableName();

        $pathView = $dirView . $dirFile . '/' . $view;
        $pathLayout = $dirView . $layout;
        $params['url'] = $this->url;
        $params['nb'] = 0;
        if(isset($_SESSION['panier']) && count($_SESSION['panier']['id_produit']) > 0) {
            $params['nb'] = array_sum($_SESSION['panier']['quantite']);
        }
        extract($params);
        ob_start(); // enclenche la mémoire tampon
            require $pathView;
            $content = ob_get_clean();
            require $pathLayout;
        return ob_end_flush(); // retourne ce qui a été retenu et arrête la mise en mémoire tampon 
    }

}