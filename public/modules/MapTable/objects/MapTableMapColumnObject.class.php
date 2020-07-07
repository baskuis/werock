<?php

/**
 * Map Table Column Object
 * Describes mapping between SQL Field and Form field
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MapTableMapColumnObject {

    public $id = null; //Unique system id
    public $appendMatch = null;
    public $dataTypeMatch = null;
    public $inputTemplate = null;
    public $fieldTemplate = null;

    /**
     * Options mapper
     *
     * @var null
     */
    public $optionMapper = null;

    /**
     * Form field modifier
     *
     * @var null
     */
    public $formFieldModifier = null;

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $appendMatch
     */
    public function setAppendMatch($appendMatch)
    {
        $this->appendMatch = $appendMatch;
    }

    /**
     * @return null
     */
    public function getAppendMatch()
    {
        return $this->appendMatch;
    }

    /**
     * @param null $dataTypeMatch
     */
    public function setDataTypeMatch($dataTypeMatch)
    {
        $this->dataTypeMatch = $dataTypeMatch;
    }

    /**
     * @return null
     */
    public function getDataTypeMatch()
    {
        return $this->dataTypeMatch;
    }

    /**
     * @param null $fieldTemplate
     */
    public function setFieldTemplate($fieldTemplate)
    {
        $this->fieldTemplate = $fieldTemplate;
    }

    /**
     * @return null
     */
    public function getFieldTemplate()
    {
        return $this->fieldTemplate;
    }

    /**
     * @param null $inputTemplate
     */
    public function setInputTemplate($inputTemplate)
    {
        $this->inputTemplate = $inputTemplate;
    }

    /**
     * @return null
     */
    public function getInputTemplate()
    {
        return $this->inputTemplate;
    }

    /**
     * @param null $optionMapper
     */
    public function setOptionMapper($optionMapper)
    {
        $this->optionMapper = $optionMapper;
    }

    /**
     * @return null
     */
    public function getOptionMapper()
    {
        return $this->optionMapper;
    }

    /**
     * @param null $formFieldModifier
     */
    public function setFormFieldModifier($formFieldModifier)
    {
        $this->formFieldModifier = $formFieldModifier;
    }

    /**
     * @return null
     */
    public function getFormFieldModifier()
    {
        return $this->formFieldModifier;
    }

}