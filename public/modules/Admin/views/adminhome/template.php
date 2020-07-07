<?php

$cacheKey = 'popularAdminPages';
if(false === ($popularAdminPages = CoreCache::getCache($cacheKey, true))){

    /** @var IntelligenceService $IntelligenceService */
    $IntelligenceService = CoreLogic::getService('IntelligenceService');

    /** @var IntelligenceDataRequestObject $IntelligenceDataRequestObject */
    $IntelligenceDataRequestObject = CoreLogic::getObject('IntelligenceDataRequestObject');
    $IntelligenceDataRequestObject->setKey(IntelligenceService::REQUEST_URI);
    $IntelligenceDataRequestObject->setFrom(strtotime('-3 days'));
    $IntelligenceDataRequestObject->setTo(time());
    $IntelligenceDataRequestObject->setLimit(200);

    /** @var array $records */
    //TODO: Find out what the performance issue is
    //$records = $IntelligenceService->getData($IntelligenceDataRequestObject);
    $records = null;

    /*
    if (!isset($records[0])) {
        CoreLog::error('No intelligence data available to optimize routes');
    }
    */

    $AdminMenu = CoreMenu::getMenuSystem(AdminModule::ADMIN_NAV_ID);

    $popularAdminPages = array();
    /** @var IntelligenceDataResponseObject $entry */
    if(isset($records[0])) {
        foreach ($records[0]->getValues() as $entry) {
            /** @var CoreMenuObject $CoreMenuObject */
            foreach($AdminMenu as $CoreMenuObject){
                if(isset($CoreMenuObject->children)){
                    /** @var CoreMenuObject $CoreMenuChildObject */
                    foreach($CoreMenuObject->children as $CoreMenuChildObject){
                        if(CoreStringUtils::compare($CoreMenuChildObject->getHref(), $entry['text'])){
                            $popularAdminPages[$CoreMenuChildObject->getId()] = $CoreMenuChildObject;
                            $popularAdminPages[$CoreMenuChildObject->getId()]->popularCount = isset($popularAdminPages[$CoreMenuChildObject->getId()]->popularCount) ? $popularAdminPages[$CoreMenuChildObject->getId()]->popularCount + (int) $entry['count'] : (int) $entry['count'];
                        }
                    }
                }
            }
        }
    }

    /** Sort them by popularity */
    usort($popularAdminPages, function ($a, $b) {
        if (!isset($a->popularCount)) $a->popularCount = 0;
        if (!isset($b->popularCount)) $b->popularCount = 0;
        if ($a->popularCount < $b->popularCount) return 1;
        if ($a->popularCount > $b->popularCount) return -1;
        return 0;
    });

    CoreCache::saveCache($cacheKey, $popularAdminPages, 3600, true);

}

$popularPagesMarkup = '
    <ol class="popularPages">';
/** @var CoreMenuObject $CoreMenuObject */
foreach($popularAdminPages as $CoreMenuObject){
    $popularPagesMarkup .= '
        <li>
            <a href="' . HTTP_PROTOCOL . DOMAIN_NAME . $CoreMenuObject->getHref() . '">' . $CoreMenuObject->getName() . '</a>
        </li>';
}
$popularPagesMarkup .= '
    </ol>';

$view = '
    <div class="admin_container">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Quick Start</h2>
                    ' . $popularPagesMarkup . '
                </div>
            </div>
        </div>
    </div>
';