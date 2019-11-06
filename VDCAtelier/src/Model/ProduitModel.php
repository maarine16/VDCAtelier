<?php

namespace Model; 

use PDO;

class produitModel extends Model {

    public function getAllproduit() { 
        return $this->findAll(); 
    }

    public function getproduitById($id) {
        return $this->find($id);
    }

    public function updateproduit($id, $info) {
        return $this->update($id, $info);
    }

    public function deleteproduit($id) {
        return $this->delete($id);
    }

    public function registerproduit($info) {
        return $this->register($info);
    }

    public function getRandProduit($nbr) {
        $requete = "SELECT * FROM " . $this->getTableName() . " ORDER BY RAND() LIMIT " . $nbr;
        $resultat = $this->getDb()->query($requete);
        $donnees = $resultat->fetchAll();

        if (!$donnees) {
            return false;
        } else {
            return $donnees;
        }
    }

    public function getAllCategories() {
        $requete = "SELECT DISTINCT category FROM " . $this->getTableName() . " ORDER BY category";
        $resultat = $this->getDb()->query($requete);
        $donnees = $resultat->fetchAll();

        if (!$donnees) {
            return false;
        } else {
            return $donnees;
        }
    }

    public function getAllproduitByCategorie($cat) {
        $requete = "SELECT * FROM " . $this->getTableName() . " WHERE category = :cat" ;
        $resultat = $this->getDb()->prepare($requete);
        $resultat->bindValue(':cat', $cat, PDO::PARAM_STR);
        $resultat->execute(); 
        $donnees = $resultat->fetchAll(PDO::FETCH_CLASS, 'Entity\\' . ucfirst($this->getTableName()));

        if (!$donnees) {
            return false;
        } else {
            return $donnees;
        }
    }

    public function getResultatRecherche($term) {
        $requete = "SELECT * FROM " . $this->getTableName() . " WHERE LOWER(title) LIKE CONCAT('%',:term,'%') OR LOWER(category) LIKE CONCAT ('%',:term,'%') OR LOWER(description) LIKE CONCAT ('%',:term,'%') OR LOWER(color) LIKE CONCAT ('%',:term,'%')";
        $resultat = $this->getDb()->prepare($requete);
        $resultat->bindValue(':term',mb_strtolower($term), PDO::PARAM_STR);
        $resultat->execute(); 
        $donnees = $resultat->fetchAll(PDO::FETCH_CLASS, 'Entity\\' . $this->getTableName());

        if (!$donnees) return false;
        else return $donnees;
    }

    public function suggestion($id,$cat){

        $requete = "SELECT * FROM " . $this->getTableName() .  " WHERE category = :cat AND id_". $this->getTableName() . " != :id" ;
        $resultat = $this->getDb()->prepare($requete);
        $resultat->bindValue(':cat',$cat,PDO::PARAM_STR);
        $resultat->bindValue(':id',$id,PDO::PARAM_INT);
        $resultat->execute();
        $donnees = $resultat->fetchAll(PDO::FETCH_CLASS,'Entity\\' . ucfirst($this->getTableName()));
        if (!$donnees) {
            return false;
        } else {
            return $donnees;
        }
    }


    public function getCodePromo($code) {
        $requete = "SELECT * FROM code_promo WHERE name = :code";
        $resultat = $this->getDb()->prepare($requete);
        $resultat->bindValue(':code', $code, PDO::PARAM_STR);
        $resultat->execute();
        $donnees = $resultat->fetchAll();
        if (!$donnees) {
            return false;
        } else {
            return $donnees;
        }
    }
}