<?php

$view = '
{{#message}}
    <div class="message_view" data-id="{{id}}" data-type="message">
        <p class="from">From <strong>{{#SendingUser}}{{username}}[{{id}}]{{/SendingUser}}</strong></p>
        <p class="to">Send to: <strong>{{#ReceivingUsers}}{{username}}[{{id}}] {{/ReceivingUsers}}</strong></p>
        <div class="body">{{{body}}}</div>
    </div>
{{/message}}
';