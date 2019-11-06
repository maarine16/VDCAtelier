<?php 

namespace Model; 

use PDO, \Manager\PDOManager;

class Model {

    public $db;

    public function __construct() {
        $this->db = PDOManager::getInstance()->getPdo();
        // je fais appel à un singleton dont je lance la méthode getPdo()
    }

    public function getDb() {
        return $this->db;
    }

    public function getTableName() {
        $table = strtolower( str_replace(array('Model\\', 'Model'), '', get_called_class()));
        /*
            get_called_class renvoit par exemple Model\produitModel
            Je remplace Model\ et Model par rien => produit
            strtolower force en minuscules => produit
        */
        return $table;
    }



    // Requête générique indépendantes de la table
    // On part du principe que le premier champ de nos tables s'appellent id_<nom_table>

    // Tout sélectionner
    public function findAll() {
        $requete = "SELECT * FROM " . $this->getTableName() ;
        $resultat = $this->getDb()->query($requete);
        $donnees = $resultat->fetchAll(PDO::FETCH_CLASS, 'Entity\\' . ucfirst($this->getTableName()));
        if (!$donnees) {
            return false;
        } else {
            return $donnees;
        }
    }

    // Séléctionnner un élément 
    public function find($id) {
        $requete = "SELECT * FROM " . $this->getTableName() . " WHERE id_" . $this->getTableName() . "= :id" ;
        $resultat = $this->getDb()->prepare($requete);
        $resultat->bindValue(':id', $id, PDO::PARAM_INT);
        $resultat->execute();
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Entity\\' . ucfirst($this->getTableName())); // FETCH_CLASS Permet d'enregistrer le resultat de donnée dans une class
        $donnees = $resultat->fetch();
        if (!$donnees) {
            return false;
        } else {
            return $donnees;
        }
    
    }

    // Suppression individuelle
    public function delete($id) {
        $requete = "DELETE FROM " . $this->getTableName() . " WHERE id_" . $this->getTableName() . "= :id" ;
        $resultat = $this->getDb()->prepare($requete);
        $resultat->bindValue(':id', $id, PDO::PARAM_INT);
        return $resultat->execute();
    }

    // Update individuelle
    public function update($id, $infos) {
        // info est un array et les index correspondent aux champ de la table
        $newValues = array();
        foreach ($infos as $key => $value) {
            $newValues[] = " $key = :$key ";
        }
        $requete = "UPDATE " . $this->getTableName() . " SET " . implode(',',$newValues) . " WHERE id_" . $this->getTableName() . "= :id";
        $infos['id'] = $id;

        $resultat = $this->getDb()->prepare($requete); 
        return $resultat->execute($infos);
    }

    // Insertion 
    public function register($infos) {
        $requete = "INSERT INTO " . $this->getTableName() . " (" . implode(',', array_keys($infos)) . ") VALUES (:" . implode (', :', array_keys($infos)) . ')';
        $resultat = $this->getDb()->prepare($requete);
        if($resultat->execute($infos)) {
            return $this->getDb()->lastInsertId();
        } else {
            return false;
        }
    }

}
