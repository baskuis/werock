<?php

$view = '
<div class="control-group" id="wrapper_for_{{name}}">
	<label class="control-label" for="{{name}}">{{label}}</label>
	<div class="controls">
	    ' . CoreTemplate::getView($data['type']) . '
	    <p class="help-block">{{helper}}</p>
	</div>
</div>';