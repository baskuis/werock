<?php

class EntityService implements EntityServiceInterface {

    private $entities = array();

    /** @var MapTableRepository $MapTableRepository */
    private $MapTableRepository;

    /** @var MapTableService $MapTableService */
    private $MapTableService;

    function __construct(){
        $this->MapTableRepository = CoreLogic::getRepository('MapTableRepository');
        $this->MapTableService = CoreLogic::getService('MapTableService');
    }

    public function getById($namespace = null, $id = null){
        $EntityObject = build($namespace);
        $record = $this->MapTableRepository->getRecordFromContext($EntityObject->_getContext());
    }

    public function build($namespace = null){

        /** @var EntityObject $EntityObject */
        $EntityObject = CoreLogic::getObject('EntityObject');
        $EntityObject->_setNamespace($namespace);

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable($namespace);

        /** @var MapTableContextObject context */
        $context = $this->MapTableService->populateContext($MapTableContextObject);

        /** set context on entity */
        $EntityObject->_setContext($context);

        /** @var array $columns */
        $columns = $context->getMapTableTableObject()->getColumns();

        /** assertion */
        if(empty($columns)){
            CoreLog::error('No columns!');
        }

        /** @var MapTableColumnObject $column */
        foreach($columns as $column){
            if(false !== ($mapping = $this->MapTableService->findMapping($column))){
                $EntityObject->addToModel($mapping, $column);
            }
        }

        /** save a reference */
        $this->entities[$namespace] = $EntityObject;

        return $EntityObject;

    }

}