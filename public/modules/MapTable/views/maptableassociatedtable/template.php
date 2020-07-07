<?php

$view = '
<div class="maptable_associated_table">
    <div class="control active">
        <strong>{{label}}</strong>
        <i class="related-icon fa fa-bars"></i>
    </div>
    <div class="body open">
        {{#options}}
        <label class="option {{#selected}}checked{{/selected}}">
            <input type="checkbox" name="associated-field-({{name}})-({{key}})" value="1" {{#selected}}checked="checked"{{/selected}} />
            {{value}}
        </label>
        {{/options}}
    </div>
</div>
';