<?php

$view = '
<div id="overlay_notifications">
    <script type="text/javascript">
        $(function(){
            handleApiNotifications();
        });
    </script>
    <span id="notification_closing">
        Closing in <span class="seconds"></span> seconds
    </span>
    <ul>
        {{#error}}
        <li class="notification_error">
            <i class="fa fa-exclamation-triangle"></i>
            {{& text}}
        </li>
        <script type="text/javascript">
            if(typeof ga !== \'undefined\'){
                ga(\'send\', \'event\', \'Generic Api Error\', \'{{text}}\', \'Notifications\');
            }
        </script>
        {{/error}}
        {{#warning}}
            <li class="notification_warning">
                <i class="fa fa-exclamation"></i>
                {{& text}}
            </li>
        {{/warning}}
        {{#success}}
        <li class="notification_success">
            <i class="fa fa-thumbs-up"></i>
            {{& text}}
        </li>
        {{/success}}
        {{#standard}}
        <li class="notification_standard">
            <i class="fa fa-info-circle"></i>
            {{& text}}
        </li>
        {{/standard}}
    </ul>
</div>
';