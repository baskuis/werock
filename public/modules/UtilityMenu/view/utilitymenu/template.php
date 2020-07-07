<?php

$view = '
<div id="utility_menu">
    <div class="utility_trigger">
        <i class="fa fa-bars"></i>
    </div>
    <div class="utility_container">
        <ul>
            ';

/**
 * Get Utility nav
 */
$utilityNav = CoreMenu::getMenuSystem(UtilityMenuModule::UTILITY_NAV_ID);

/**
 * Build menu items
 */
foreach($utilityNav as $CoreMenuObject){

    /**
     * @var \CoreMenuObject $item
     */
    $view .= '
            <li>
                ' . CoreTemplate::render($CoreMenuObject->getTemplate(), $CoreMenuObject) . '
            </li>';
}

$view .= '
        </ul>
    </div>
</div>
';