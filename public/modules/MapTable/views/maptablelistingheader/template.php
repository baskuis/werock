<?php

$requires = array('MapTableContextObject');

$view = '
<div class="listing-header">
    <form action="' . CoreArrayUtils::getString(array(), array('action' => '')) . '" method="get">
        ' . CoreArrayUtils::hiddenFields(array(), array('search' => '')) . '
        <input type="text" name="search" class="search-field" value="' . CoreStringUtils::strip($data['MapTableContextObject']->searchQuery, '"') . '" />
        <input type="hidden" name="start" value="0" />
        <a href="' . CoreArrayUtils::getString(array(), array('search' => '')) . '"><span class="clear-search glyphicon glyphicon-remove-sign"></span></a>
    </form>
    <a href="' . CoreArrayUtils::getString(array('action' => 'create'), array('primary_value' => '')) . '" class="add-link">
        <span class="glyphicon glyphicon-plus"></span>
    </a>
</div>
';