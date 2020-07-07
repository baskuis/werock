<?php

/** @var array $requires */
$requires = array_merge($requires, array('widgets'));

$view = '
<div class="listings-column">
    ' . $view . '
</div>
<div class="widget-column">
    ';
    if(!empty($data['widgets'])){
        foreach($data['widgets'] as $widget){
            $view .= $widget->render();
        }
    }
    $view .= '
</div>';