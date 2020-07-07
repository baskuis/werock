<?php
$view = '

<input name="{{name}}" type="text" class="form-control" value="{{default_value}}" placeholder="{{placeholder}}" {{#disabled}}disabled="disabled"{{/disabled}} />
{{#default_value}}
<p style="text-align: right; margin: 5px;">
    <a href="{{default_value}}" target="_blank"><i class="fa fa-link"></i> open</a>
</p>
{{/default_value}}
';