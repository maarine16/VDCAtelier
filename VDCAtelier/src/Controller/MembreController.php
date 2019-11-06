<?php 

namespace Controller;

class MembreController extends Controller {
 

    public function cryptMdp($password) {
        $salt = 'Comp!iqu3';
        $password_new = md5($password . $salt);
        return $password_new;
    }

    public function createSession($membre) {
        $_SESSION['membre'] = $membre;
        $_SESSION['membre']->setField('password','');
    }

    public function connexion() {
        if(isset($_SESSION['membre'])) {
            $this->redirect($this->url);
        }

        $erreur = array();

        if(!empty($_POST)) {
            if (empty($_POST['email'])) {
                $erreur[] = 'Merci de saisir votre email';
            } 
            if (empty($_POST['password'])) {
                $erreur[] = 'Merci de saisir votre mot de passe';
            } 
            if(empty($erreur)) {
                if ($membre = $this->getModel()->existsEmail($_POST['email'])) {
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

        $params['title'] = 'Connexion';
        $params['id'] = 'connexion';
        $params['erreur'] = (!empty($erreur)) ? implode ('<br>',$erreur) : '';
        return $this->render('layout.html','connexion.html',$params);
    }


    public function inscription() {

        if(!empty($_POST)) {
            $erreur = array();
            // champ vide 
            $champsvides = 0;
            foreach ($_POST as $value) {
                if(empty(trim($value))) $champsvides ++ ;
            }
            if ($champsvides > 0) {
                $erreur[] = 'Il y a ' . $champsvides . ' champ(s) manquant(s).';
            }

            if($this->getModel()->existsEmail($_POST['email'])) {
                $erreur[] = 'Cet email est déjà utilisé';
            }

            if(empty($erreur)) {
                // Je n'ai pas d'erreur
                $_POST['password'] = $this->cryptMdp($_POST['password']);
                $_POST['role'] = 0; 
                $id_user = $this->getModel()->inscription($_POST);
                $membre = $this->getModel()->find($id_user);
                $this->createSession($membre);
                $this->redirect($this->url);
            }

        }

        $params['title']  = 'inscription';
        $params['id'] = 'inscription';
        $params['erreur'] = (!empty($erreur)) ? implode('<br>', $erreur) : '';
        $this->render('layout.html','inscription.html', $params);
    }




    public function monCompte() {
        if(isset($_SESSION['membre'])) {
            $params['id'] = 'user';

            return $this->render('layout.html', 'mon_compte.html', $params);
        } else {
            $this->redirect($this->url . 'membre/connexion');
        }
    }

    public function modifProfil($arg) {
        if(isset($_SESSION['membre'])) {
            $id_membre = $_SESSION['membre']->getField('id_membre');

            if(!empty($_POST) && isset($_POST['valid_info'])) {
                $champsvides = 0;
                foreach($_POST as $value) {
                    if(empty($value)) $champsvides++ ;
                }
                if($champsvides == 0) {
                    unset($_POST['valid_info']); 

                    $this->getModel()->update($id_membre,$_POST);

                    $membre = $this->getModel()->find($id_membre);
                    $this->createSession($membre);
                    $this->redirect($this->url . 'membre/moncompte');

                }
            }

            if($arg == 'coord') {$params['action'] = 'edit_info'; }
            $params['id'] = 'user';
            return $this->render('layout.html', 'mon_compte.html', $params);

        } else {
            $this->redirect($this->url . 'membre/connexion');
        }
    }

    public function mesidentifiants() {
        if(isset($_SESSION['membre'])) {
            $id_membre = $_SESSION['membre']->getField('id_membre');

            $params['id'] = 'user';
            return $this->render('layout.html', 'identifiant.html', $params);

        } else {
            $this->redirect($this->url . 'membre/connexion');
        }
    }

    public function modif_password($arg) {
        if(isset($_SESSION['membre'])) {
            $id_membre = $_SESSION['membre']->getField('id_membre');

            $erreur = array();

            if(!empty($_POST) && isset($_POST['valid_password'])) {
                if(isset($_POST['password']) && isset($_POST['confirmation-password'])) {
                    if ($_POST['password'] === $_POST['confirmation-password']){
                        $new_password = $this->cryptMdp($_POST['password']);
                        $this->getModel()->update($id_membre, array('password' => $new_password));

                        $this->redirect($this->url . 'membre/mesidentifiants'); 
                    } else {

                        $erreur[] = 'Confirmation de mot de passe incorrect';

                    }
                }
            }

            if($arg == 'password') {$params['action'] = 'edit_password'; }
            $params['id'] = 'user';
            $params['erreur'] = (!empty($erreur)) ? implode ('<br>',$erreur) : '';
            return $this->render('layout.html', 'identifiant.html', $params);

        } else {
            $this->redirect($this->url . 'membre/connexion');
        }
    }

    public function deconnexion() {
        unset($_SESSION['membre']);
        $this->redirect($this->url . 'membre/connexion');
    }


    public function adminMembre() {
        if (isset($_SESSION['membre']) && $_SESSION['membre']->isAdmin()) {
            $params['title'] = 'Gestion des membres';
            $params['membres'] = $this->getModel()->findAll();
            $this->render('layout.html','adminMembre.html', $params);
        }
    }

    public function editStatut($id_membre) {

        if (isset($_SESSION['membre']) && $_SESSION['membre']->isAdmin() && $id_membre != $_SESSION['membre']->getField('id_membre')) {
            $membre_courant = $this->getModel()->find($id_membre);
            $new_statut = ($membre_courant->getField('statut') == 0) ? 1 : 0;
            $this->getModel()->update($id_membre,array('statut' => $new_statut));
        }

        $this->redirect($this->url . 'membre/adminMembre');
    }

}