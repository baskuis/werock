<?php

$requires = array('pagination');

$view = '';
if($data['pagination']->start > 0){
    if(0 > ($newStart = $data['pagination']->start - $data['pagination']->length)){
        $newStart = 0;
    }
    $view .= '
    <a href="' . CoreArrayUtils::getString(array($data['pagination']->startKey => $newStart)) . '" title="' . CoreLanguage::get('maptable.pagination.previous') . '">
        <span class="glyphicon glyphicon-chevron-up"></span>
    </a>';

}