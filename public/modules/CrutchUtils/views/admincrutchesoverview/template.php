<?php

$view = '
<div class="content">
    <h1>{{title}}</h1>
    {{#description}}<p>{{description}}</p>{{/description}}
    ';
    foreach(CoreCrutches::$crutches as $key => $crutchlist){
        foreach($crutchlist as $crutchKey => $theCrutch){
            $view .= '<div class="well">';
            $view .= '<p><h3><span class="glyphicon glyphicon-cog"></span> ' . $theCrutch[0]->getName() . '</h3></p>';
            $view .= '<ul>';
            foreach($theCrutch as $index => $crutchComponent){

                /** @var CoreCrutchObject $crutchComponent */
                $view .= '<li>' . $crutchComponent->getWebPath() . $crutchComponent->getFile() . '</li>';

            }
            $view .= '</ul>';
            $view .= '</div>';

        }
    }
$view .= '
</div>';