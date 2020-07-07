<?php

$view = '
' . CoreTemplate::getView('admincurrentuser') . '
<h1>' . CoreProp::get('site.name', 'WeRock') . '</h1>
' . CoreTemplate::getView('adminnavsearch') . '
<div id="admin-nav-scrollable-wrapper">
<ul class="nav-sidebar">';

/**
 * Get Utility nav
 */
$utilityNav = CoreMenu::getMenuSystem(AdminModule::ADMIN_NAV_ID);

/**
 * Build menu items
 */
foreach($utilityNav as $item){

    /**
     * @var \CoreMenuObject $item
     */
    $view .= CoreTemplate::render($item->getTemplate(), $item);

}

$view .= '</ul>
</div>';