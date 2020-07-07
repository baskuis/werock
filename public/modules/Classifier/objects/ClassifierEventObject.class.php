<?php

class ClassifierEventObject {

    /** @var string $name */
    public $name;

    /** @var array $properties */
    public $properties;

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
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add property
     *
     * @param ClassifierEventPropertyObject $classifierEventPropertyObject
     */
    public function addProperty(ClassifierEventPropertyObject $classifierEventPropertyObject){
        array_push($this->properties, $classifierEventPropertyObject);
    }

    /**
     * @param mixed $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

}