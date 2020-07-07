<?php

/**
 * Core menu object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreMenuObject {

    public $name;
    public $href;
    public $title;
    public $id;
    public $classes;
    public $template;
    public $onclick;
    public $attributes;
    public $parentId;
    public $zIndex;
    public $children;
    /** @var bool $haveChildren */
    public $haveChildren;
    public $active;
    public $hide;
    public $target;

    /**
     * Build a simple instance of
     * CoreMenuObject
     *
     * @param string $id
     * @param string $url
     * @param string $name
     * @param string $targetMenuId
     * @param string $parentId
     * @param string $template
     * @param int $zIndex
     * @return CoreMenuObject
     */
    public static function buildSimple($id = null, $url = null, $name = null, $targetMenuId = null, $parentId = null, $template = null, $zIndex = 99){
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId($id);
        $CoreMenuObject->setHref($url);
        $CoreMenuObject->setName($name);
        $CoreMenuObject->setTitle($name);
        $CoreMenuObject->setTarget($targetMenuId);
        $CoreMenuObject->setParentId($parentId);
        $CoreMenuObject->setTemplate($template);
        $CoreMenuObject->setZIndex((int) $zIndex);
        return $CoreMenuObject;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param mixed $classes
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return mixed
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param mixed $href
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @return mixed
     */
    public function getHref()
    {
        return $this->href;
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
    public function getId()
    {
        return $this->id;
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

    /**
     * @param mixed $onclick
     */
    public function setOnclick($onclick)
    {
        $this->onclick = $onclick;
    }

    /**
     * @return mixed
     */
    public function getOnclick()
    {
        return $this->onclick;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param mixed $zIndex
     */
    public function setZIndex($zIndex)
    {
        $this->zIndex = $zIndex;
    }

    /**
     * @return mixed
     */
    public function getZIndex()
    {
        return $this->zIndex;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->haveChildren = !empty($children);
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return boolean
     */
    public function isHaveChildren()
    {
        return $this->haveChildren;
    }

    /**
     * @param boolean $haveChildren
     */
    public function setHaveChildren($haveChildren)
    {
        $this->haveChildren = $haveChildren;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getHide()
    {
        return $this->hide;
    }

    /**
     * @param mixed $hide
     */
    public function setHide($hide)
    {
        $this->hide = $hide;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

}