<?php

$view = '
<div class="content">
    <h1>{{title}}</h1>
    {{#description}}<p>{{description}}</p>{{/description}}
    ' . CoreTemplate::getView('formnotifications') . '
    ' . CoreForm::getForm('interpretschema')->getFullForm() . '
    {{#SchemaOutline}}
    <h2>Embeddable schema.json table component</h2>
    <p>Copy the below table definition into the schema.json file of the module to which this data belongs to allow for portability.</p>
    <pre>{{{SchemaOutline}}}</pre>
    {{/SchemaOutline}}
</div>
';
