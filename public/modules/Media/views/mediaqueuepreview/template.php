<?php

$view = '
<span style="display: none;" class="data" data-id="{{id}}" data-name="{{name}}" data-stream="{{stream}}" data-download="{{download}}"><!-- data --></span>
{{^isImage}}<span class="icon glyphicon glyphicon-file"></span>{{/isImage}}
{{#isImage}}<span class="icon glyphicon glyphicon-picture"></span>{{/isImage}}
<span class="name"><a href="{{stream}}" target="_blank">{{name}}</a></span>
<span class="size">{{niceSize}}</span>
<span class="done glyphicon glyphicon-ok-circle"></span>
<span class="remove glyphicon glyphicon-remove-circle"></span>
{{#isImage}}<span class="image"><img src="{{stream}}" /></span>{{/isImage}}
';