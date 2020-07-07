<?php

class ExperimentationVariantObject {

    /** @var ExperimentationObject $ExperimentationObject */
    public $ExperimentationObject;

    /** @var int $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var string $description */
    public $description;

    /** @var string $type */
    public $type;

    /** @var string $javascript */
    public $javascript;

    /** @var ExperimentationVariantEntrySummaryObject $ExperimentationVariantEntrySummaryObject */
    public $ExperimentationVariantEntrySummaryObject;

    /** @var bool $base */
    public $base = false;

    /** @var bool $selected */
    public $selected = false;

    /** @var string $template */
    public $template;

    /** @var string $dateAdded */
    public $dateAdded;

    /** @var string $lastModified */
    public $lastModified;

    /**
     * @return ExperimentationObject
     */
    public function getExperimentationObject()
    {
        return $this->ExperimentationObject;
    }

    /**
     * @param ExperimentationObject $ExperimentationObject
     */
    public function setExperimentationObject($ExperimentationObject)
    {
        $this->ExperimentationObject = $ExperimentationObject;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * @param string $javascript
     */
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;
    }

    /**
     * @return ExperimentationVariantEntrySummaryObject
     */
    public function getExperimentationVariantEntrySummaryObject()
    {
        return $this->ExperimentationVariantEntrySummaryObject;
    }

    /**
     * @param ExperimentationVariantEntrySummaryObject $ExperimentationVariantEntrySummaryObject
     */
    public function setExperimentationVariantEntrySummaryObject($ExperimentationVariantEntrySummaryObject)
    {
        $this->ExperimentationVariantEntrySummaryObject = $ExperimentationVariantEntrySummaryObject;
    }

    /**
     * @return boolean
     */
    public function isBase()
    {
        return $this->base;
    }

    /**
     * @param boolean $base
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * @return boolean
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * @param boolean $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }
    
    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param string $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return string
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param string $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }

}