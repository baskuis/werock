<?php

$view = '
<input name="{{name}}" type="hidden" value="{{default_value}}" {{#disabled}}disabled="disabled"{{/disabled}} />
<div class="visitor-info">
{{#data.visitor}}
    <div class="line">
        <strong>ID:</strong> {{default_value}}
    </div>
    <div class="line">
        <strong>IP:</strong> {{ip}} {{#ip}}<a href="http://ipaddressdetective.com/{{ip}}.html" title="show location of {{ip}}" target="_blank"><span class="glyphicon glyphicon-globe"></span></a>{{/ip}}
    </div>
    <div class="line">
        <strong>Hits:</strong> {{hits}}
    </div>
        {{#data}}
    <div class="line">
         <strong>Browser:</strong> {{browser}}
    </div>
    <div class="line">
         <strong>Version:</strong> {{version}}
    </div>
    <div class="line">
        <strong>Platform:</strong> {{platform}}
    </div>
    <div class="line last">
        <strong>Context:</strong> {{#isMobile}}mobile{{/isMobile}}{{^isMobile}}desktop{{/isMobile}}
    </div>
        {{/data}}
{{/data.visitor}}
{{^data.visitor}}
    <div class="no-visitor">
        No visitor information available for id {{default_value}}
    </div>
{{/data.visitor}}
</div>';