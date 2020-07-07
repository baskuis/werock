<?php

$view = '
<div class="control-group">
	{{#data}}
    <div class="info">
        <p><span class="label label-default">{{module.name}}</span><br /></p>
	    <p><em>{{module.version}}</em></p>
	    <p>{{module.description}}</p>
	    ';
if(isset($data['data']['module']->dependencies) && !empty($data['data']['module']->dependencies)) {
	$view .= '<p><strong>Dependencies:</strong>';
	foreach ($data['data']['module']->dependencies as $dep => $range) {
		$view .= $dep . ' <em>' . $range['min'] . '-' . $range['max'] . '</em> ';
	}
	$view .= '</p>';
}
$view .= '
	</div>
	{{/data}}
	<label class="control-label" for="{{name}}">{{label}}</label>
	<div class="controls">
	    {{#data}}
	    {{^isDependency}}' . CoreTemplate::getView($data['type']) . '{{/isDependency}}
	    {{#isDependency}}<em style="color: red;">is dependency</em>{{/isDependency}}
	    {{/data}}
	    <p class="help-block">{{helper}}</p>
	</div>
    <div style="clear: both;"></div>
</div>
<div class="line"></div>';