<?php

class EmailSmtpAuthenticationObject {

    private $server = null;
    private $port = null;
    private $username = null;
    private $password = null;

    public function setServer($server = null){
        $this->server = $server;
    }
    public function setPort($port = null){
        $this->port = $port;
    }
    public function setUsername($username = null){
        $this->username = $username;
    }
    public function setPassword($password = null){
        $this->password = $password;
    }

    public function getServer(){
        return $this->server;
    }
    public function getPort(){
        return $this->port;
    }
    public function getUsername(){
        return $this->username;
    }
    public function getPassword(){
        return $this->password;
    }

}