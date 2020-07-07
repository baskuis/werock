<?php

class EmailAddresseeObject {

    public $name = null;
    public $email = null;

    public function __construct($name = null, $email = null){
        $this->name = $name;
        $this->email = $email;
    }

    public function setName($name = null){
        $this->name = $name;
    }
    public function setEmail($email = null){
        $this->email = $email;
    }
    public function getName(){
        return $this->name;
    }
    public function getEmail(){
        return $this->email;
    }

    public function __toString(){
        return $this->name . '<' . $this->email . '>';
    }

}