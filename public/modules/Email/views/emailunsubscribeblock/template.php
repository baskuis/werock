<?php

$view = '
<p style="color: #999999; font-size: 12px;">
    If you would like to stop receiving any email from ' . SITE_NAME . '. Please
    <a href="' . HTTP_PROTOCOL . DOMAIN_NAME . '/email/unsubscribe?email=' . urlencode($data['email']) . '&amp;token=' . urlencode($data['token']) . '">un-subscribe</a>.
</p>
';