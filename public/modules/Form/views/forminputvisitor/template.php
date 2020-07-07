<?php

$view = '
<div class="visitor-info">
{{#data.visitor}}
    <div class="line">
        <strong>ID:</strong> {{default_value}}
    </div>
    <div class="line">
        <strong>IP:</strong> {{ip}}
    </div>
    <div class="line last">
        <strong>Hits:</strong> {{hits}}
    </div>
{{/data.visitor}}
{{^data.visitor}}
    <div class="no-visitor">
        No visitor information available for id {{default_value}}
    </div>
{{/data.visitor}}
</div>';