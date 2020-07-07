<?php

$view = '

<select name="{{name}}" class="form-control" {{#disabled}}disabled="disabled"{{/disabled}}>
    <option value="0">Off</option>
    <option value="1" {{#default_value}}selected="selected"{{/default_value}}>On</option>
</select>';
