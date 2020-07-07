<?php

class MapTableAssociatedTableDescriptionObject {

    public $table;
    public $title;
    public $description;

    public $inputTemplate = 'maptableassociatedtable';
    public $fieldTemplate = 'formfieldnaked';

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return mixed
     */
    public function getInputTemplate()
    {
        return $this->inputTemplate;
    }

    /**
     * @param mixed $inputTemplate
     */
    public function setInputTemplate($inputTemplate)
    {
        $this->inputTemplate = $inputTemplate;
    }

    /**
     * @return mixed
     */
    public function getFieldTemplate()
    {
        return $this->fieldTemplate;
    }

    /**
     * @param mixed $fieldTemplate
     */
    public function setFieldTemplate($fieldTemplate)
    {
        $this->fieldTemplate = $fieldTemplate;
    }

}