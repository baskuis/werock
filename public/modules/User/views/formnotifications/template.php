<?php

//view
$view = '
{{#notifications}}
	<div class="notifications_block">	    
	    <ul>
	        {{#error}}
	        <li class="notification_{{type}} alert alert-danger"><i class="fa fa-warning"></i> {{&text}} <i class="close fa fa-times-circle-o"></i></li>
	        {{/error}}
	        {{#warning}}
	        <li class="notification_{{type}} alert alert-warning"><i class="fa fa-warning"></i> {{&text}} <i class="close fa fa-times-circle-o"></i></li>
	        {{/warning}}
	        {{#success}}
	        <li class="notification_{{type}} alert alert-success"><i class="fa fa-check"></i> {{&text}} <i class="close fa fa-times-circle-o"></i></li>
	        {{/success}}
	        {{#standard}}
	        <li class="notification_{{type}} alert alert-info"><i class="fa fa-asterisk"></i> {{&text}} <i class="close fa fa-times-circle-o"></i></li>
	        {{/standard}}
	    </ul>
	</div>
{{/notifications}}
';

$script = '
try {
    {{#error}}
        if(typeof ga !== \'undefined\'){
            ga(\'send\', \'event\', \'Generic Form Error\', \'{{text}}\', \'Notifications\');
        }
    {{/error}}
} catch(e){
    console.log(e);
}
';