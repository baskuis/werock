<?php

/**
 * Map Table Column Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MapTableColumnObject {

    public $field;
    public $type;
    public $collation;
    public $null;
    public $key;
    public $default;
    public $extra;
    public $privileges;
    public $extensions;
    public $comment;

    public $submittedValue;

    /** @var MapTableTableObject $relatedTable */
    public $relatedTable;

    /**
     * @var string $label
     */
    public $label;

    /**
     * @var string $validation
     */
    public $validation;

    /**
     * @var string $placeholder
     */
    public $placeholder;

    /**
     * @var string $helper
     */
    public $helper;

    /**
     * @param mixed $collation
     */
    public function setCollation($collation)
    {
        $this->collation = $collation;
    }

    /**
     * @return mixed
     */
    public function getCollation()
    {
        return $this->collation;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {

        $this->comment = $comment;

        /**
         * Extract data from comment
         */
        if(!empty($comment)){
            self::interpretComment();
        }

    }

    /**
     * Interpret comment
     */
    public function interpretComment(){

        /**
         * Comment parts
         */
        parse_str($this->comment);

        /**
         * Set label
         */
        if(isset($label)){
            $this->label = $label;
        }

        /**
         * Validation
         */
        if(isset($validation)){
            $this->validation = $validation;
        }

        /**
         * Placeholder
         */
        if(isset($placeholder)){
            $this->placeholder = $placeholder;
        }

        /**
         * Helper
         */
        if(isset($helper)){
            $this->helper = $helper;
        }

        /**
         * Extensions
         */
        if(isset($extensions)){
            $this->extensions = $extensions;
        }

    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $null
     */
    public function setNull($null)
    {
        $this->null = $null;
    }

    /**
     * @return mixed
     */
    public function getNull()
    {
        return $this->null;
    }

    /**
     * @param mixed $privileges
     */
    public function setPrivileges($privileges)
    {
        $this->privileges = $privileges;
    }

    /**
     * @return mixed
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $validation
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;
    }

    /**
     * @return string
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param string $helper
     */
    public function setHelper($helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @param mixed $submittedValue
     */
    public function setSubmittedValue($submittedValue)
    {
        $this->submittedValue = $submittedValue;
    }

    /**
     * @return mixed
     */
    public function getSubmittedValue()
    {
        return $this->submittedValue;
    }

    /**
     * @param mixed $extensions
     */
    public function setExtensions($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @return mixed
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @return MapTableTableObject
     */
    public function getRelatedTable()
    {
        return $this->relatedTable;
    }

    /**
     * @param MapTableTableObject $relatedTable
     */
    public function setRelatedTable($relatedTable)
    {
        $this->relatedTable = $relatedTable;
    }

}