<?php

$requires = array('pagination');

$view = '';

if($data['pagination']->start + $data['pagination']->length < $data['pagination']->count){
    $newStart = $data['pagination']->start + $data['pagination']->length;
    $view .= '
    <a href="' . CoreArrayUtils::getString(array($data['pagination']->startKey => $newStart)) . '" title="' . CoreLanguage::get('maptable.pagination.next') . '">
        <span class="glyphicon glyphicon-chevron-down"></span>
    </a>';
}