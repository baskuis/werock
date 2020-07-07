<?php

$view = '
<div class="name group {{#last}}last{{/last}} {{#super}}super{{/super}} {{#isApplied}}checked="applied"{{/isApplied}}" data-id="{{id}}" {{#super}}onclick="javascript:alert(\'This groups entitlements cannot be changed.\');"{{/super}}>
    <label>
        <input name="group_{{id}}_{{urn}}" class="security_group_option" data-id="{{id}}" data-urn="{{urn}}" type="checkbox" {{#super}}checked="checked"{{/super}} {{#super}}disabled="disabled"{{/super}} {{#isApplied}}checked="checked"{{/isApplied}} />
        {{name}}
        <span class="description">{{description}}</span>
        <span class="pull-right icon-group">
            {{#isMember}}<i class="fa fa-user" title="You are a member"></i>{{/isMember}}
            {{#isOwner}}<i class="fa fa-users" title="You are an owner"></i>{{/isOwner}}
            {{#isSystem}}<i class="fa fa-lock" title="This is a system group"></i>{{/isSystem}}
            {{^isSystem}}<i class="fa fa-unlock" title="This is a custom group"></i>{{/isSystem}}
        </span>
    </label>
    <ul class="entitlements">
    {{#entitlements}}
        <li>
            <label>
                <input name="entitlement_{{urn}}" value="{{urn}}" type="checkbox" {{#super}}checked="checked"{{/super}} data-child-urns="{{#childUrns}}{{.}},{{/childUrns}}" {{#isApplied}}checked="checked"{{/isApplied}} {{#super}}disabled="disabled"{{/super}} />
                {{name}}
            </label>
        </li>
    {{/entitlements}}
    </ul>
</div>
';