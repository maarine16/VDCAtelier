<?php 

namespace Entity;

class produit {
    private $id_produit ;
    private $titre ;
    private $category ;
    private $description ;
    private $price_ht ;
    private $photo ;
    private $reference ;
    private $public ;
    private $size ;
    private $color ;
    private $stock ;
    private $nbr_point;
    private $tva;
    private $price_TTC;

    public function getField($propriete) {
        return $this->$propriete;
    }

    public function setField($propriete,$value) {
        $this->$propriete = $value;
    }

    public function getFields() {
        return get_object_vars($this); 
    }
    
    public function priceTTC() {
        $montant_tva = ($this->tva * $this->price_ht) / 100;
        $price_TTC = $this->price_ht + $montant_tva;
        return $price_TTC;
    }


}