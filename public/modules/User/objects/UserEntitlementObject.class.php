<?php

class UserEntitlementObject {

    public $urn;
    public $name;
    public $description;
    public $type;

    /** @var array $parents */
    public $parents = array();

    /**
     * @return mixed
     */
    public function getUrn()
    {
        return $this->urn;
    }

    /**
     * @param mixed $urn
     */
    public function setUrn($urn)
    {
        $this->urn = $urn;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return UserEntitlementObject
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @param UserEntitlementObject $parent
     */
    public function addParent($parent){
        array_push($this->parents, $parent);
    }

    public function setParents($parents){
        $this->parents = $parents;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param null $urn
     */
    public function hasParentUrn($urn = null)
    {
        $that = $this;
        do {
            $parents = $that->getParents();
            if(!empty($parents)) {
                foreach($parents as $parent) {
                    if($parent) {
                        $that = $parent;
                        if ($that->getUrn() == $urn) {
                            return true;
                        }
                    }
                }
            }
        } while(!empty($parent));
        return false;
    }

}