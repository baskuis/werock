<?php

$requires = array('widgets');

/** @var IntelligenceWidgetObject $widget */
foreach($data['widgets'] as $widget){
    $view .= $widget->render();
}