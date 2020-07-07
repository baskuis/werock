<?php

$view = '
<div class="content">
    <h1>{{title}}</h1>
    <p>{{description}}</p>
    ' . CoreTemplate::getView('formnotifications') . '
    ' . CoreForm::getForm('elasticsearchconnectiondetails')->getFullForm() . '
    {{#elasticsearchStatus}}
    <h2>ElasticSearch Status</h2>
    <pre>{{{elasticsearchStatus}}}</pre>
    {{/elasticsearchStatus}}
</div>';