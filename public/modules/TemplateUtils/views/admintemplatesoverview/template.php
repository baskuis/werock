<?php

$view = '
<div class="content">';

/** @var CoreTemplateObject $CoreTemplateObject */
foreach(CoreTemplate::$coreTemplates as $CoreTemplateObject){

    /**
     * Get assets reference
     */
    $CoreInitAssetsReferenceObject = $CoreTemplateObject->getCoreAssetsReferenceObject();

    /**
     * Build the view
     */
    $view .= '<div class="well">';

    $view .= '<h3><span class="glyphicon glyphicon-cog"></span> ' . $CoreTemplateObject->getNamespace();
    if($CoreTemplateObject->hasType('wrap')){
        $view .= ' <span class="label label-default">wrapped</span>';
    }
    if($CoreTemplateObject->hasType('plain')){
        $view .= ' <span class="label label-default">plain</span>';
    }
    $view .= '</h3>';

    if($CoreTemplateObject->hasCrutches()){
        $view .= '<p><strong>Requires:</strong> <span class="label label-default">' . implode('</span>, <span class="label label-default">', $CoreTemplateObject->getCrutches()) . '</span></p>';
    }
    $view .= '<p><strong>File:</strong> ' . $CoreTemplateObject->getTemplatePath() . '</p>';
    $view .= '<h4>Source</h4>';
    $view .= '<pre>' . highlight_string(file_get_contents($CoreTemplateObject->getTemplatePath()), true) . '</pre>';

    /**
     * Drop in the admin assets reference template
     */
    $view .= CoreTemplate::render('adminassetsreference', $CoreInitAssetsReferenceObject);

    $view .= '</div>';

}

$view .= '</div>';