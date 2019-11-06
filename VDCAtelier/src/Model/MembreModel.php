<?php

namespace Model; 

use PDO;

class MembreModel extends Model {

    public function existsEmail($email) {
        $requete = "SELECT * FROM " . $this->getTableName() . ' WHERE email = :email';
        $resultat = $this->getDb()->prepare($requete);
        $resultat->bindValue(':email',$email,PDO::PARAM_STR);
        $resultat->execute();
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Entity\\' . $this->getTableName());
        $donnees = $resultat->fetch();

        if ($donnees) return $donnees;
        else return false;
    }


    
}