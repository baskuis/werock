<?php
$view = '
<h4>Assets</h4>
<ul>
    {{#crutches}}<li>{{name}} {{type}}</li>{{/crutches}}
    {{#less}}<li>less: {{path}}</li>{{/less}}
    {{#scss}}<li>scss: {{.}}</li>{{/scss}}
    {{#templates}}<li>template: [{{namespace}}] {{templatePath}}</li>{{/templates}}
    {{#scripts}}<li>script: {{.}}</li>{{/scripts}}
</ul>';