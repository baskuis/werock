<?php

class EngagementEmailEntryObject {

    public $id;
    public $name;
    public $emailValue;
    public $lastSent;
    public $tag;
    public $date;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmailValue()
    {
        return $this->emailValue;
    }

    /**
     * @param mixed $emailValue
     */
    public function setEmailValue($emailValue)
    {
        $this->emailValue = $emailValue;
    }

    /**
     * @return mixed
     */
    public function getLastSent()
    {
        return $this->lastSent;
    }

    /**
     * @param mixed $lastSent
     */
    public function setLastSent($lastSent)
    {
        $this->lastSent = $lastSent;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

}