<?php 

namespace Entity;

class Membre {
    public $id ;
    public $email ;
    public $password ;
    public $nom ;
    public $prenom ;
    public $civility ;
    public $city ;
    public $postal_code ;
    public $address ;
    public $role ;
    
    public function getField($propriete) {
        return $this->$propriete;
    }
    
    public function setField($propriete,$value) {
        $this->$propriete = $value;
    }
    
    public function isAdmin(){
        if ($this->getField('role') == 1) {
            return true;
        } else {
            return false;
        }
    }
}

