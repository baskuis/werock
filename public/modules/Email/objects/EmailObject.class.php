<?php

class EmailObject {

    public $sentform = null;
    public $addressees = array();

    public $subject = null;
    public $htmlbody = null;
    public $textbody = null;

    public function setSubject($subject = null){
        $this->subject = $subject;
    }
    public function setAddressees($addressees = array()){
        $this->addressees = $addressees;
    }
    public function addAddressee(EmailAddresseeObject $addressee = null){
        $this->addressees[] = $addressee;
    }
    public function setSentfrom(EmailAddresseeObject $sentfrom = null){
        $this->sentform = $sentfrom;
    }
    public function setHtmlBody($htmlbody = null){
        $this->htmlbody = $htmlbody;
    }
    public function setTextBody($textbody = null){
        $this->textbody = $textbody;
    }

    public function getSubject(){
        return $this->subject;
    }
    public function getAddressees(){
        return $this->addressees;
    }
    public function getSentfrom(){
        return $this->sentform;
    }
    public function getHtmlBody(){
        return $this->htmlbody;
    }
    public function getTextBody(){
        return $this->textbody;
    }

}

