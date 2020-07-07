<?php

interface CoreUserObject {
    public function getId();
    public function setId($id);
    public function getUsername();
    public function setUsername($username);
    public function getFirstname();
    public function setFirstname($firstname);
    public function getLastname();
    public function setLastname($lastname);
}