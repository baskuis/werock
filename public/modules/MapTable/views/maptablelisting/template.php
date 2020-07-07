<?php

$requires = array('listings', 'MapTableContextObject');

$view = '
' . CoreTemplate::render('maptablelistingheader') . '
<div class="row row-no-padding listings-wrapper">
    <div class="col-md-12">
        ' . CoreTemplate::render('maptablelistingpaginationtop') . '
        <ul class="listing">';
    if(!empty($data['listings'])){
        /** @var MapTableListingRowObject $listing */
        foreach($data['listings'] as $listing){
            $selected = ($data['MapTableContextObject']->primaryValue == $listing->getId());
            $view .= '
            <li class="' . ($selected ? 'selected' : '') . '">
                <a href="' . CoreArrayUtils::getString(array('action' => 'delete', 'primary_value' => (int) $listing->getId())) . '" class="delete">
                    <span class="glyphicon glyphicon-remove-circle"></span>
                </a>
                <a href="' . CoreArrayUtils::getString(array('action' => 'edit', 'primary_value' => (int) $listing->getId())) . '">
                    <span class="name compressable compress">' . CoreHtmlUtils::escape($listing->getName()) . '</span>
                    <span class="description compressable truncate">' . CoreHtmlUtils::escape($listing->getDescription()) . '</span>
                    <span class="extra compressable truncate">' . CoreHtmlUtils::escape($listing->getExtra()) . '</span>
                    <span class="glyphicon glyphicon-chevron-right right-arrow"></span>
                    <span class="date-added">' . date('\<\s\t\r\o\n\g\>M j\<\/\s\t\r\o\n\g\> g:ia', strtotime($listing->getDateAdded())) . '</span>
                </a>
                ' . ($selected ? '<span class="arrow-decal"></span>' : '') . '
            </li>
        ';
        }
    }else{
        $view .= '
            <li class="no-records">
                No records
            </li>';
    }
    $view .= '
        </ul>
        ' . CoreTemplate::render('maptablelistingpaginationbottom') . '
    </div>
</div>';