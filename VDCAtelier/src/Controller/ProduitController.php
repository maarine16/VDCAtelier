<?php 

namespace Controller;

class produitController extends Controller {


    //
    // "/"
    //
    public function index() {
        $produits = $this->getModel()->getRandProduit(6);

        $params = array (
            'produits' => $produits,
            'id' => 'index',
        );
        return $this->render('layout.html', 'index.html', $params);
    }


    //
    // "/produit/articles"
    //
    public function articles(){
        $produits = $this->getModel()->getAllproduit();
        $categories = $this->getModel()->getAllCategories();
        $params = array (
            'produits' => $produits,
            'categories' => $categories,
            'id' => 'category',
        );
        return $this->render('layout.html', 'articles.html', $params);
    }


    //
    // "/produit/categorie/$cat"
    //
    public function categorie($cat) {
        $produits = $this->getModel()->getAllproduitByCategorie($cat);
        $categories = $this->getModel()->getAllCategories();

        $params = array (
            'produits' => $produits,
            'categories' => $categories,
            'id' => 'category',
        );
        return $this->render('layout.html', 'articles.html', $params);
    }

    
    //
    // "/produit/affiche/$id"
    //
    public function affiche($id) {
        $message = '';
        $produit = $this->getModel()->getproduitById($id);

        // traité un ajoute au panier
        if(!empty($_POST)) {
            $this->ajoutproduitPanier($_POST['id_produit'],$_POST['quantite']);
            $message = 'Le produit a été ajouté au panier';
        }

        // suggestion de produit
        $categorie_produit = $produit->getField('category');
        $produits_suggerer = $this->getModel()->suggestion($id,$categorie_produit);


        $params = array (
            'produit' => $produit,
            'message' => $message,
            'produits_suggerer' => $produits_suggerer,
            'id' => 'product',
        );
        return $this->render('layout.html', 'produit.html', $params);
    }

    //
    // "/produit/recherche"
    //
    public function recherche() {
        if(!empty($_POST['term'])) {
            $produits = $this->getModel()->getResultatRecherche($_POST['term']);
    
            $nbResultat = ($produits) ? count($produits) : 0 ; 
            $title = 'Recherche de ' . $_POST['term'];

            $params = array (
                'title' => $title,
                'produits' => $produits,
                'nbResultat' => $nbResultat,
                'id' => 'search',
            );
            return $this->render('layout.html', 'recherche.html', $params);

        } else {
                $this->afficheAll();
        }
    }



    //
    // Creation panier
    //
    public function creationPanier() {
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier']['id_produit'] = array();
            $_SESSION['panier']['quantite'] = array();
            $_SESSION['panier']['prix'] = array();
        }
    }
    
    //
    // Ajout produit au panier
    //
    public function ajoutproduitPanier($id_produit,$quantite) {
        $this->creationPanier(); 
        $positionproduit = array_search($id_produit,$_SESSION['panier']['id_produit']);
        if($positionproduit !== false) {
            $_SESSION['panier']['quantite'][$positionproduit] += $quantite ;
        } else {
            $_SESSION['panier']['id_produit'][] = $id_produit;
            $_SESSION['panier']['quantite'][] = $quantite; 
            $_SESSION['panier']['prix'][] = $this->getModel()->getproduitById($id_produit)->getField('price_ht');
        }
    }


    //
    // "/produit/panier"
    //
    public function panier() {
        if(!empty($_POST['supp_article'])) {
            $this->suppArticlePanier();
        }
        if(!empty($_SESSION['panier'])) {
            $params['content_panier'] = array();

            for ($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
                $params['content_panier'][$i] = $this->getModel()->getproduitById($_SESSION['panier']['id_produit'][$i]);
            }

            $montant_HT = 0;
            $tva = 0;
     
            for($i=0; $i<sizeof($_SESSION['panier']['id_produit']); $i++) {
                $prix_articles = $_SESSION['panier']['prix'][$i] * $_SESSION['panier']['quantite'][$i];
                $montant_HT += $prix_articles;

                $tva_produit =  $params['content_panier'][$i]->getField('tva');
                $montant_tva_produit = ($_SESSION['panier']['prix'][$i] * $_SESSION['panier']['quantite'][$i]) * $tva_produit / 100;
                $tva += $montant_tva_produit;

                $fdp = 0;
                if ($params['content_panier'][$i]->getField('category') == 'motifs') {
                    $fdp += 0;
                } else {
                    $fdp += 5.80;
                }
            }
            $montant_TTC = $montant_HT + $tva;

            $params['fdp'] = $fdp;
            $params['montant_TTC'] = $montant_TTC;
            $params['prix_articles'] = $prix_articles;
            $params['montant_HT'] = $montant_HT;
            $params['tva'] = $tva;
        }

        $params['id'] = 'panier';

        return $this->render('layout.html','panier.html',$params);
    }
    // if(!empty($_POST['viderpanier'])) {
    //     $this->viderPanier();
    // }
    // if(!empty($_SESSION['panier'])) {
    //     $params['content_panier'] = array();

    //     for ($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
    //         $params['content_panier'][$i] = $this->getModel()->getproduitById($_SESSION['panier']['id_produit'][$i]);
    //     }

    //     $montant_HT = 0;
    //     $tva = 0;
    //     for($i=0; $i<sizeof($_SESSION['panier']['id_produit']); $i++) {
    //         $prix_articles = $_SESSION['panier']['prix'][$i] * $_SESSION['panier']['quantite'][$i];
    //         $montant_HT += $prix_articles;

    //         $tva_produit =  $params['content_panier'][$i]->getField('tva');
    //     }
    // }
    // $params = array(
    //     'id' => 'panier',
    //     'montant_HT' => $montant_HT,
    //     'tva' => $tva_produit
    // );
    // return $this->render('layout.html','panier.html',$params);
    // Méthodes liés au panier


    //
    // Suppression 1 article panier
    //
    public function suppArticlePanier($id) {
        if(isset($_SESSION['panier'])) {
            $article_supp = $_SESSION['panier'][$id];
            unset($article_supp);
        }
    }
    // public function viderPanier() {
    //     if(isset($_SESSION['panier'])) {
    //         unset($_SESSION['panier']);
    //     }
    // }


    //
    // Code promo panier
    //
    public function codePromo() {
        if(!empty($_GET['indice'])){
            $code = $_GET['indice'];
            $code_promo = $this->getModel()->getCodePromo($code);
            
            return $code_promo;
        }
    }
    // if(!empty($_GET['indice']) || $_GET['indice'] == 0){
    //     include('connect.php');
    //     $requete = "SELECT texte FROM messages";
    //     $reponse = $pdo->query($requete);
    //     $liste_textes = ($reponse->fetchAll());
    //     if($_GET['indice'] < $reponse->rowCount()){
    //         echo $liste_textes[$_GET['indice']]['texte'];
    //     }
    // }









    // Méthode admin de gestion de produit 
    public function adminproduit() {
        // tester si je suis admin 
        if (isset($_SESSION['membre']) && $_SESSION['membre']->isAdmin()) {
            $params['title'] = 'Admin produit';
            $params['produits'] = $this->getModel()->getAllproduit();
            return $this->render('layout.html','adminproduit.html',$params);
        } else {
            $this->redirect($this->url . 'membre/connexion');
        }
    }
    // suppression d'un produit
    public function suppproduit($id) {
        if (isset($_SESSION['membre']) && $_SESSION['membre']->isAdmin()) {
            $this->getModel()->deleteproduit($id);
            $params['title'] = 'Admin produit';
            $params['produits'] = $this->getModel()->getAllproduit();
            $params['message'] = '<div class="alert alert-success text-center col-12">Le produit a été supprimé </div>';

            return $this->render('layout.html','adminproduit.html',$params);
        } else {
            $this->redirect($this->url . 'membre/connexion');
        }
    }
    // ajout d'un produit
    public function ajoutproduit() {
        if (isset($_SESSION['membre']) && $_SESSION['membre']->isAdmin()) {

            $params['erreur'] = array();
            if(!empty($_POST)) {
                $champsvides = 0;
                foreach($_POST as $value) {
                    if(empty($value)) $champsvides ++;
                }
                if($champsvides > 0) {
                    $erreur[] = 'Merci de remplir les ' . $champsvides . ' champ(s) manquant(s)';
                }

                if(empty($erreur)) {
                    $this->copyPhoto();
                    $this->getModel()->registerproduit($_POST);
                    $this->redirect($this->url . 'produit/adminproduit');
                }   
            }

            $params['title'] = 'Ajout d\'un produit';
            $params['erreur'] = (!empty($erreur)) ? implode('<br>',$erreur) : '';
            $params['produit_actuel'] = $_POST; // permet de remplir les champs si $_POST a été remplis
            return $this->render('layout.html','ajoutproduit.html',$params);
        } else {
            $this->redirect($this->url . 'membre/connexion');
        }
    }

    // Modification d'un produit
    public function editproduit($id) {
        if (isset($_SESSION['membre']) && $_SESSION['membre']->isAdmin()) {

            $params['erreur'] = array();
            if(!empty($_POST)) {
                $champsvides = 0;
                foreach($_POST as $value) {
                    if(empty($value)) $champsvides ++;
                }
                if($champsvides > 0) {
                    $erreur[] = 'Merci de remplir les ' . $champsvides . ' champ(s) manquant(s)';
                }

                if(empty($erreur)) {
                    $this->copyPhoto();
                    $this->getModel()->updateproduit($id,$_POST);
                    $this->redirect($this->url . 'produit/adminproduit');
                }   
            }

            $params['title'] = 'Modification d\'un produit';
            $params['erreur'] = (!empty($erreur)) ? implode('<br>',$erreur) : '';
            $params['produit_actuel'] = (!empty($_POST)) ? $_POST : $this->getModel()->getproduitById($id)->getFields(); // permet de remplir les champs si $_POST a été remplis
            $params['produit_actuel']['photo'] = $this->getModel()->getproduitById($id)->getField('photo');
            return $this->render('layout.html','ajoutproduit.html',$params);
        } else {
            $this->redirect($this->url . 'membre/connexion');
        }
    }
    // Méthode pour ajouté une photo en bdd
    public function copyPhoto() {
        if(!empty($_FILES['photo']['name'])) {
            $nom = $_POST['reference'].'-'.$_FILES['photo']['name'];
            $_POST['photo'] = $nom;
            $pathPhoto = __DIR__ . '/../../web/photo/' . $nom;
            move_uploaded_file($_FILES['photo']['tmp_name'],$pathPhoto);
        }
    }
    
    public function majStocks(){
        if(isset($_SESSION['panier'])) {
            for ($i = 0; $i < count($_SESSION['panier']['id_produit']) ; $i++) {
                $id_produit = $_SESSION['panier']['id_produit'][$i];
                $produit = $this->getModel()->getproduitById( $id_produit);
                $new_stock = $produit->getField('stock') - $_SESSION['panier']['quantite'][$i];
                $this->getModel()->updateproduit($id_produit,array(
                    'stock' => $new_stock ));
            }
            //destruction panier
            $this->viderPanier();
        }
        $this->redirect($this->url.'commande/afficheCommande');
    }


}