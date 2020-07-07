<?php

$view = '
<li class="nav-section {{#active}}active{{/active}}">
    <a class="nav-section-link open" href="javascript: void(0);" title="{{title}}">
        <strong>{{name}}</strong>
        <span class="glyphicon glyphicon-chevron-down"></span>
    </a>
    <ul class="admin-nav-section-items">
        {{#children}}
        <li class="{{#active}}active{{/active}}">
            <a href="{{href}}" title="{{title}}">{{name}}</a>
        </li>
        {{/children}}
    </ul>
</li>
';

