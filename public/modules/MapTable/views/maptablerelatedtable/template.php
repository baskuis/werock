<?php

$requires = array('mapTableLightContextObject');

$view = '
<div class="maptable_related_table">
    <div class="control">
        {{label}}
        <i class="related-icon fa fa-bars"></i>
    </div>
    <div class="body">
        <iframe src="/maptable/related/table/{{name}}?context=' . CoreEncryptionUtils::encryptString(json_encode($data['mapTableLightContextObject'])) . '" scrolling="no" seamless="seamless"></iframe>
    </div>
</div>';