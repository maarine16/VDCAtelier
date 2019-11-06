<?php 

namespace Controller;

use Entity\Membre;
use Controller\MembreController;
use Model\MembreModel;


class AdminController extends Controller {



    public function connexion() {

var_dump($_SESSION['membre']);

        if(isset($_SESSION['membre']) && $_SESSION['membre']['role'] == 1) {
            $this->redirect($this->url.'admin/accueil');
        }

        $erreur = array();

        if(!empty($_POST)) {

            // Je tente d'accéder au model membre pour avoir les requêtes qui y sont écrites
            // Si je n'y arrive, il faudra les écrires dans le model Admin comme en commentaires

            // $verif =  $this->getOtherModel('MembreModel');
            // var_dump('<pre>'.$verif.' ok3 </pre>');

            if (empty($_POST['email'])) {
                $erreur[] = 'Merci de saisir votre email';
            } 
            if (empty($_POST['password'])) {
                $erreur[] = 'Merci de saisir votre mot de passe';
            } 
            if(empty($erreur)) {
                if ($membre = $this->getOtherModel('MembreModel')->existsEmail($_POST['email'])) {
                    if ($membre->getField('password') == $this->cryptMdp($_POST['password'])) {
                        $this->createSession($membre);
                        $this->redirect($this->url);
                    } else {
                        $erreur[] = 'Erreur sur les identifiants';
                    }
                } else {
                    $erreur[] = 'Erreur sur les identifiants';
                }
            }
        }
    
        $params['id'] = 'connect_admin';
        $params['erreur'] = (!empty($erreur)) ? implode ('<br>',$erreur) : '';

        return $this->render('layout_admin.html','admin.html', $params);

    }

    public function accueil() {
        
        $params['id'] = 'admin_accueil';

        return $this->render('layout_admin.html','admin_accueil.html', $params);


    }

}