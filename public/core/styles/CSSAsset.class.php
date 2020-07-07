<?php

/**
 * CSS Asset
 * Provides logic that stacks, compiles and cached less/scss
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CSSAsset {

    /**
     * @var String path
     * @var String templateName
     */
    public $path;
    public $webPath;
    public $templateName;

    /**
     * @var String type
     */
    public $type;

    /**
     * Types (possible types)
     */
    const TYPE_SCSS = 'scss';
    const TYPE_LESS = 'less';

    /**
     * Constants
     */
    const EMPTY_STRING = '';

    /**
     * Constructor
     *
     * @param $path
     * @param $templateName
     * @param $type
     * @throws Exception
     */
    function __construct($path = null, $templateName = null, $type = null){

        //assertions
        if(!$path) throw new Exception('Need path to stack template');
        if($type != self::TYPE_LESS && $type != self::TYPE_SCSS) throw new Exception('Need a valid CSSAsset type');

        //set properties
        $this->setPath($path);
        $this->setTemplateName($templateName);
        $this->setType($type);

    }

    /**
     * @param mixed $path
     */
    public function setPath($path){
        $this->path = $path;
        $this->webPath = str_ireplace(DOCUMENT_ROOT, self::EMPTY_STRING, $this->path);
    }

    /**
     * @return mixed
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getWebPath()
    {
        return $this->webPath;
    }

    /**
     * @param mixed $webPath
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;
    }

    /**
     * @param mixed $templateName
     */
    public function setTemplateName($templateName){
        $this->templateName = $templateName;
    }

    /**
     * @return mixed
     */
    public function getTemplateName(){
        return $this->templateName;
    }

    /**
     * Have template name
     *
     * @return boolean
     */
    public function haveTemplateName(){
        return ($this->templateName);
    }

    /**
     * @param mixed $type
     */
    public function setType($type){
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType(){
        return $this->type;
    }

}