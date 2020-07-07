<?php

$view = '
<div class="message_row" data-id="{{id}}" data-type="message">
    <span class="from">From: <strong>{{#SendingUser}}{{username}}[{{id}}]{{/SendingUser}}</strong></span>
    <span class="excerpt">{{plainTextExcerpt}}</span>
    <span class="time"></span>
</div>
';