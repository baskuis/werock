<?php

/**
 * Media Create Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MediaCreateObject {

    public $filename;
    public $data;
    public $type;
    public $size;
    public $hash;
    public $backgroundColor;
    public $width;
    public $height;

    /**
     * @param mixed $data
     */
    public function setData($data)
    {

        /**
         * Check base64
         */
        try {
            $data = str_replace(' ','+',$data);
            $data = base64_decode($data);
        } catch(Exception $e){
            CoreNotification::set(CoreNotification::ERROR, 'Unable to detect/handle base64 data encoding');
        }

        /**
         * Get file size
         */
        if (function_exists('mb_strlen')){
            $this->size = mb_strlen($data, '8bit');
        } else {
            $this->size = strlen($data);
        }

        /**
         * Set data
         */
        $this->data = $data;

        /**
         * Set hash signature
         */
        $this->hash = md5($this->data);

    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
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
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getBackgroundColor()
    {
        return !empty($this->backgroundColor) ? $this->backgroundColor : 'FFFFFF';
    }

    /**
     * @param mixed $backgroundColor
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

}