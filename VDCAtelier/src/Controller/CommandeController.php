<?php

namespace Controller;

class CommandeController extends Controller{

	public $id_commande;

	public function mesCommandes(){

		if(isset($_POST['validerpanier'])){
			$this->validationCommande();
		}


		$params['id'] = 'user';
		$params['commandes'] = $this->getModel()->getMesCommandes();
		return $this->render('layout.html','commande.html',$params);
	}



	public function validationCommande(){
		//panier devient une commande
		if(isset($_SESSION['panier'])){
			// insertoin de la commande
			$montant=0;
			for ($i = 0; $i < count($_SESSION['panier']['id_produit']) ; $i++) {
				$montant += $_SESSION['panier']['prix'][$i] * $_SESSION['panier']['quantite'][$i];
				
			}
			$infos = array(
				'id_membre' => $_SESSION['membre']->getField('id_membre'),
				'montant' => $montant,
				'date_enregistrement' => date('Y-m-d H:i:s'),
				'etat' => 'en cours de traitement'
			);
			$this->id_commande = $this->getModel()->registerCommande($infos);
			// insertion des details de la commande
			for ($i = 0; $i < count($_SESSION['panier']['id_produit']) ; $i++) {
				$details = array(
					'id_commande' => $this->id_commande, 
					'id_produit' =>  $_SESSION['panier']['id_produit'][$i],
					'quantite' =>  $_SESSION['panier']['quantite'][$i],
					'prix' =>  $_SESSION['panier']['prix'][$i]

				);
				$this->getModel()->registerDetails($details);
			}
			// appel aux methodes liees au produit
			$this->redirect($this->url.'produit/majStocks');
		}	
	}



	public function afficheDetailsCommande($id_commande){
		if (!empty($id_commande) && isset($_SESSION['membre'])) {

			$commande = $this->getModel()->find($id_commande);

			if (!empty($commande) && $commande->getField('id_membre') == $_SESSION['membre']->getField('id_membre')){ 


			$params['title'] = 'Details de la commande';
			$params['commande'] = $this->getModel()->find($id_commande);
			$params['details_commande'] = $this->getModel()->getDetails($id_commande);
			} else {
				$params['title'] = 'commande introuvable';
			}
		    return $this->render('layout.html','detailscommande.html',$params);	
		} else {
			$this->redirect($this->url .'commande/afficheCommande');
		}
	}




    public function adminCommande() {
        if (isset($_SESSION['membre']) && $_SESSION['membre']->isAdmin()) {
            $params['title'] = 'Toutes les commandes';
            $commandes = $this->getModel()->getAllCommandes(); 
            if(!empty($commandes)) {
                $params['commandes'] = $commandes;
                foreach($commandes as $commande){
                    $params['details_commandes'][$commande['id_commande']] = $this->getModel()->getDetails($commande['id_commande']);
                }
            }
        } else {
            $params['title'] = 'Accès non autorisé';
        }
        $this->render('layout.html', 'adminCommande.html', $params);
    }

    public function changeStatut($param) {
        if(isset($_SESSION['membre']) && $_SESSION['membre']->isAdmin()) {
            // exemple: $params : 5-2
            //                    id_commande - nouvelle étape (1,2 ou 3)
            //                    1 = en cours de traitement
            //                    2 = envoyé
            //                    3 = livré

            $tab = explode('-',$param); 
            $id_commande = $tab[0];
            switch($tab[1]) {
                case 1 : $newEtat = 'en cours de traitement'; break;
                case 2 : $newEtat = 'envoyé'; break;
                case 3 : $newEtat = 'livré'; break;
            }
            $this->getModel()->updateStatutCommande($id_commande,$newEtat);            
        }
        $this->redirect($this->url.'commande/adminCommande');
    }


}