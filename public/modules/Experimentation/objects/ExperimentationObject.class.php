<?php

class ExperimentationObject {

    const TYPE_TEMPLATE = 'template';
    const TYPE_JS = 'js';

    /** @var int $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var string $description */
    public $description;

    /** @var string $type (template, js) */
    public $type;

    /** @var IntelligenceDataObject $intelligenceDataObject */
    public $intelligenceDataObject;

    /** @var int $intelligenceDataId */
    public $intelligenceDataId;

    /** @var int $minHists */
    public $minHists;

    /** @var string $template */
    public $template;

    /** @var int $groupId */
    public $groupId;

    /** @var UserGroupObject $UserGroupObject */
    public $UserGroupObject;

    /**
     * Types
     */
    const TYPE_MATCH = 'match';
    const TYPE_REGEX = 'regex';

    /** @var string $matchType */
    public $matchType;

    /** @var string $matchValue */
    public $matchValue;

    /** @var array $variants */
    public $variants;

    /** @var ExperimentationVariantObject $base */
    public $base;

    /** @var ExperimentationVariantObject $variant */
    public $variant;

    /** @var string $dateAdded */
    public $dateAdded;

    /** @var $lastModified */
    public $lastModified;

    /** @var ExperimentationSummaryObject $ExperimentationSummaryObject */
    public $ExperimentationSummaryObject;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if($type != self::TYPE_TEMPLATE && $type != self::TYPE_JS){
            CoreLog::error('Invalid type: ' . $type);
        }
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return IntelligenceDataObject
     */
    public function getIntelligenceDataObject()
    {
        return $this->intelligenceDataObject;
    }

    /**
     * @param IntelligenceDataObject $intelligenceDataObject
     */
    public function setIntelligenceDataObject($intelligenceDataObject)
    {
        $this->intelligenceDataObject = $intelligenceDataObject;
    }

    /**
     * @return int
     */
    public function getIntelligenceDataId()
    {
        return $this->intelligenceDataId;
    }

    /**
     * @param int $intelligenceDataId
     */
    public function setIntelligenceDataId($intelligenceDataId)
    {
        $this->intelligenceDataId = $intelligenceDataId;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return UserGroupObject
     */
    public function getUserGroupObject()
    {
        return $this->UserGroupObject;
    }

    /**
     * @param UserGroupObject $UserGroupObject
     */
    public function setUserGroupObject($UserGroupObject)
    {
        $this->UserGroupObject = $UserGroupObject;
    }

    /**
     * @return string
     */
    public function getMatchType()
    {
        return $this->matchType;
    }

    /**
     * @param string $matchType
     */
    public function setMatchType($matchType)
    {
        if($matchType != self::TYPE_MATCH && $matchType != self::TYPE_REGEX){
            CoreLog::error('Invalid mathType: ' . $matchType);
        }
        $this->matchType = $matchType;
    }

    /**
     * @return string
     */
    public function getMatchValue()
    {
        return $this->matchValue;
    }

    /**
     * @param string $matchValue
     */
    public function setMatchValue($matchValue)
    {
        $this->matchValue = $matchValue;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param array $variants
     */
    public function setVariants($variants)
    {
        /** @var ExperimentationVariantObject $variant */
        foreach($variants as &$variant){
            if($this->template == $variant->getTemplate()){
                $this->base = $variant;
                $variant->setBase(true);
            }
        }
        $this->variants = $variants;
    }

    /**
     * @return int
     */
    public function getMinHists()
    {
        return $this->minHists;
    }

    /**
     * @param int $minHists
     */
    public function setMinHists($minHists)
    {
        $this->minHists = $minHists;
    }

    /**
     * @return ExperimentationVariantObject
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @param ExperimentationVariantObject $base
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * @return ExperimentationVariantObject
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * @param ExperimentationVariantObject $variant
     */
    public function setVariant($variant)
    {
        if(!empty($this->variants)){
            /** @var ExperimentationVariantObject $v */
            foreach($this->variants as &$v){
                if($v->getId() == $variant->getId()){
                    $v->setSelected(true);
                }
            }
        }
        $this->variant = $variant;
    }

    /**
     * @return mixed
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param mixed $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }

    /**
     * @return string
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param string $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return ExperimentationSummaryObject
     */
    public function getExperimentationSummaryObject()
    {
        return $this->ExperimentationSummaryObject;
    }

    /**
     * @param ExperimentationSummaryObject $ExperimentationSummaryObject
     */
    public function setExperimentationSummaryObject($ExperimentationSummaryObject)
    {
        $this->ExperimentationSummaryObject = $ExperimentationSummaryObject;
    }

    public function buildSummary(){

        $exposures = 0;
        $conversions = 0;

        /** @var ExperimentationVariantObject $v */
        foreach($this->variants as $v){
            $s = $v->getExperimentationVariantEntrySummaryObject();
            if(empty($s)){
                throw new Exception('ExperimentationVariantEntrySummaryObject not set');
            }
            $exposures += $s->getExposures();
            $conversions += $s->getConversions();
        }

        /** @var ExperimentationSummaryObject $ExperimentationSummaryObject */
        $this->ExperimentationSummaryObject = CoreLogic::getObject('ExperimentationSummaryObject');
        $this->ExperimentationSummaryObject->setExposures($exposures);
        $this->ExperimentationSummaryObject->setConversions($conversions);

        /** @var ExperimentationVariantObject $b */
        $b = $this->getBase();
		if(empty($b)){
			CoreNotification::set('No variants configured', CoreNotification::ERROR);
			return;
		}

        /** @var ExperimentationVariantEntrySummaryObject $bs */
        $bs = $b->getExperimentationVariantEntrySummaryObject();
        $this->ExperimentationSummaryObject->setBaseConversionRate($bs->getConversionRatePercentage());

        /** @var array $variants */
        $variants = $this->getVariants();

        /** @var bool $conclusive */
        $conclusive = false;

        $highestLowerBound = 0;
        $lowestUpperBound = 1;
        $count = 0;

        /** @var ExperimentationVariantObject $variant */
        foreach($variants as &$variant){
            $s = $variant->getExperimentationVariantEntrySummaryObject();
            $count += $s->getExposures();
            if(empty($s)){
                CoreLog::error('No ExperimentationVariantEntrySummaryObject summary');
            }
            if($s->getLowerBound() > $highestLowerBound){
                $highestLowerBound = $s->getLowerBound();
            }
            if($s->getUpperBound() < $lowestUpperBound){
                $lowestUpperBound = $s->getUpperBound();
            }
        }

        if($highestLowerBound > $lowestUpperBound && $count > 300){
            $conclusive = true;
        }

        $this->ExperimentationSummaryObject->setConclusive($conclusive);

    }

}