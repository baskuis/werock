<?php

/**
 * Core Rendering Template Interface
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface CoreRenderTemplateInterface {

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus();

    /**
     * Get routes for this action
     *
     * @return array
     */
    public function getRoutes();

    /**
     * Register the model
     *
     * @return mixed
     */
    public function register();

    /**
     * Build the model
     *
     * @param $params
     * @return mixed
     */
    public function build($params);

	public function setTitle($value);
	public function getTitle();
	
	public function setDescription($value);
	public function getDescription();
	
	public function setTemplate($value);
	public function getTemplate();

    /**
     * Execute template
     *
     * @return mixed
     */
    public function execute();
	
}