<?php
$view = '
<select class="chosen form-control" name="{{name}}" {{#disabled}}disabled="disabled"{{/disabled}}>
    <option value="">-- select value --</option>
    {{#options}}
        <option value="{{key}}" {{#selected}}selected="selected"{{/selected}}>{{{value}}}</option>
    {{/options}}
</select>';