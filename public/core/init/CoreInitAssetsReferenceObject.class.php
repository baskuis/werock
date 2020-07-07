<?php

/**
 * Core Init Assets Reference Object
 * This is the object where asset details are maintained
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreInitAssetsReferenceObject {

    public $name;
    public $context;

    public $less;
    public $scss;
    public $crutches;

    public $scripts;

    /**
     * @param mixed $crutches
     */
    public function setCrutches($crutches)
    {
        $this->crutches = $crutches;
    }

    /**
     * @return mixed
     */
    public function getCrutches()
    {
        return $this->crutches;
    }

    /**
     * @param mixed $less
     */
    public function setLess($less)
    {
        $this->less = $less;
    }

    /**
     * @return mixed
     */
    public function getLess()
    {
        return $this->less;
    }

    /**
     * @param mixed $scripts
     */
    public function setScripts($scripts)
    {
        $this->scripts = $scripts;
    }

    /**
     * @return mixed
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * @param mixed $scss
     */
    public function setScss($scss)
    {
        $this->scss = $scss;
    }

    /**
     * @return mixed
     */
    public function getScss()
    {
        return $this->scss;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
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
    public function getName()
    {
        return $this->name;
    }

}