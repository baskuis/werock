<?php

$view = '
<select name="{{name}}" class="form-control">
    <option value="">-- select value --</option>
    {{#options}}
        <option value="{{key}}" {{#selected}}selected="selected"{{/selected}}>{{value}}</option>
    {{/options}}
</select>';