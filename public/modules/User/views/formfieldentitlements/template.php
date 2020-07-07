<?php

$view = '
<div class="form_entitlements">
    <div class="control active">
        <strong>{{label}}</strong>
        <i class="related-icon fa fa-bars"></i>
    </div>
    <input type="hidden" name="{{name}}" value="{{default_value}}" class="form_entitlements_field" />
    <div class="body open">
        {{#options}}
        <label class="option {{#selected}}checked{{/selected}} {{#first}}first{{/first}} {{#last}}last{{/last}}">
            <input type="checkbox" name="{{key}}" value="{{value}}" {{#selected}}checked="checked"{{/selected}} data-child-urns="{{#childUrns}}{{.}},{{/childUrns}}" />
            {{value}}
            {{#description}}<span class="description">{{description}}</span>{{/description}}
        </label>
        {{/options}}
    </div>
</div>';