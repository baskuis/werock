<?php

/**
 * Core Template Object
 * This object holds a template definition
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreTemplateObject {

    /**
     * @var null
     */
    public $namespace = null;

    /**
     * @var array
     */
    public $crutches = array();

    /**
     * @var array
     */
    public $requires = array();

    /**
     * @var int
     */
    public $index = 0;

    /**
     * @var null
     */
    public $type = null;

    /**
     * @var null
     */
    public $script = null;

    /**
     * @var null
     */
    public $templatePath = null;

    /**
     * @var null
     */
    public $crutchesPath = null;

    /**
     * @var null
     */
    public $typePath = null;

    /**
     * @var null
     */
    public $scriptPath = null;

    /**
     * @var null
     */
    public $basePath = null;

    /**
     * @var CoreInitAssetsReferenceObject $coreAssetsReferenceObject
     */
    public $coreAssetsReferenceObject = null;

    /** @var CoreTemplateObject $parent */
    public $parent;

    /**
     * @var null
     */
    public $indexPath = null;

    public function setNamespace($name = null){
        $this->namespace = $name;
    }
    public function setCrutches($crutches = array()){
        $this->crutches = $crutches;
    }
    public function setRequired($requires = array()){
        $this->requires = $requires;
    }
    public function setIndex($index = 0){
        $this->index = $index;
    }
    public function setType($type = null){
        $this->type = $type;
    }
    public function setTemplatePath($path = null){
        $this->templatePath = $path;
    }
    public function setCrutchesPath($path = null){
        $this->crutchesPath = $path;
    }
    public function setIndexPath($path = null){
        $this->indexPath = $path;
    }
    public function setTypePath($path = null){
        $this->typePath = $path;
    }

    public function getNamespace(){
        return $this->namespace;
    }
    public function getCrutches(){
        return $this->crutches;
    }
    public function getRequires(){
        return $this->requires;
    }
    public function getIndex(){
        return $this->index;
    }
    public function getType(){
        return $this->type;
    }
    public function getTemplatePath(){
        return $this->templatePath;
    }
    public function getCrutchesPath(){
        return $this->crutchesPath;
    }
    public function getIndexPath(){
        return $this->indexPath;
    }
    public function getTypePath(){
        return $this->typePath;
    }
    public function getBasePath()
    {
        return $this->basePath;
    }
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return CoreTemplateObject
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param CoreTemplateObject $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param CoreInitAssetsReferenceObject $coreAssetsReferenceObject
     */
    public function setCoreAssetsReferenceObject(CoreInitAssetsReferenceObject $coreAssetsReferenceObject)
    {
        $this->coreAssetsReferenceObject = $coreAssetsReferenceObject;
    }

    /**
     * @return CoreInitAssetsReferenceObject
     */
    public function getCoreAssetsReferenceObject()
    {
        return $this->coreAssetsReferenceObject;
    }


    /**
     * See if template has a certain type
     *
     * @param null $type
     * @return bool
     */
    public function hasType($type = null){
        return is_array($this->type) ? in_array($type, $this->type) : ($type == $this->type);
    }

    /**
     * Has crutches helper
     *
     * @return bool
     */
    public function hasCrutches(){
        return !empty($this->crutches);
    }

    /**
     * @return null
     */
    public function getScriptPath()
    {
        return $this->scriptPath;
    }

    /**
     * @param null $scriptPath
     */
    public function setScriptPath($scriptPath)
    {
        $this->scriptPath = $scriptPath;
    }

    /**
     * @return null
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * @param null $script
     */
    public function setScript($script)
    {
        $this->script = $script;
    }

}