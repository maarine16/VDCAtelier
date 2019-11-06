<?php

namespace Model;

use PDO;

class CommandeModel extends Model{
  
	public function registerCommande($infos){
		return $this->register($infos);
	}

	public function registerDetails($infos){
        $requete = "INSERT INTO details_". $this->getTableName()."(".implode(',', array_keys($infos)).") VALUES (:" . implode(', :', array_keys($infos)).")";
        $resultat = $this->getDb()->prepare($requete);
        if( $resultat->execute($infos)){
            return $this->getDb()->lastInsertId();
        } else {
            return false;
        }
	}
	public function getMesCommandes(){
		if ( isset($_SESSION['membre'] ) ){
			$monId = $_SESSION['membre']->getField('id_membre');
			$requete = "SELECT * FROM " . $this->getTableName() . " WHERE id_membre = :id_membre";
			$resultat = $this->getDb()->prepare($requete);
			$resultat->bindValue(':id_membre',$monId,PDO::PARAM_INT);
			$resultat->execute();
   			$donnees = $resultat->fetchAll(PDO::FETCH_CLASS,'Entity\\' . ucfirst($this->getTableName()));
   			if($donnees) return $donnees;
    		else return false;
		}
	}

	public function getDetails($id_commande){
		$requete = "SELECT d.*, p.titre , p.photo, p.reference FROM details_" .$this->getTableName()." d, produit p WHERE d.id_produit = p.id_produit AND id_".$this->getTableName()." = :id_commande ";
		$resultat = $this->getDb()->prepare($requete);
		$resultat->bindValue(':id_commande',$id_commande,PDO::PARAM_INT);
		$resultat->execute();
   		$donnees = $resultat->fetchAll();
   		if($donnees) return $donnees;
    	else return false;

	}

    public function getAllCommandes() {
        $requete = "SELECT * FROM " . $this->getTableName() . " c, membre m 
        WHERE c.id_membre = m.id_membre";
        $resultat = $this->getDb()->query($requete);
        $donnees = $resultat->fetchAll();
        if(!$donnees) return false;
        else return $donnees;
    }


    public function updateStatutCommande($id,$etat) { // nom personnalisé
        $this->update($id,array('etat' => $etat)); // nom standard
    }
    


}