<?php

$view = '
Here are the messages <a href="/messages/write">write message</a><br /><br />

<div id="werock_messages_list">
    <!-- messages load here -->
</div>
' . CoreTemplate::getView('messageviewer');