<?php 

namespace Entity;

class Commande {
    private $id_commande ;
    private $id_membre ;
    private $montant ;
    private $date_enregistrement ;
    private $etat ;

    public function getField($propriete) {
        return $this->$propriete;
    }

    public function setField($propriete,$value) {
        $this->$propriete = $value;
    }

}