<?php

namespace Model; 

use PDO;
use Entity\Membre;

class AdminModel extends Model {


    // public function existsEmail($email) {
    //     $requete = "SELECT * FROM membre  WHERE email = :email";
    //     $resultat = $this->getDb()->prepare($requete);
    //     $resultat->bindValue(':email',$email,PDO::PARAM_STR);
    //     $resultat->execute();
    //     $resultat->setFetchMode(PDO::FETCH_CLASS, 'Entity\Membre');
    //     $donnees = $resultat->fetch();

    //     if ($donnees) return $donnees;
    //     else return false;
    // }


}