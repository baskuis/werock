<?php

class EntityObject {

    /** @var MapTableContextObject $context */
    private $_context;

    /** @var array */
    private $_model;

    private $_namespace;

    /**
     * @return mixed
     */
    public function _getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function _setNamespace($namespace)
    {
        $this->_namespace = $namespace;
    }

    /**
     * @return MapTableContextObject
     */
    public function _getContext()
    {
        return $this->_context;
    }

    /**
     * @param MapTableContextObject $context
     */
    public function _setContext($context)
    {
        $this->_context = $context;
    }

    public function addToModel(MapTableMapColumnObject $mapTableMapColumnObject, MapTableColumnObject $mapTableColumnObject){

        /** @var EntityColumnObject $EntityColumnObject */
        $EntityColumnObject = CoreLogic::getObject('EntityColumnObject');
        $EntityColumnObject->setMapping($mapTableMapColumnObject);
        $EntityColumnObject->getColumn($mapTableColumnObject);

        /** stack it */
        $this->_model[$mapTableColumnObject->getField()] = $EntityColumnObject;

    }

}