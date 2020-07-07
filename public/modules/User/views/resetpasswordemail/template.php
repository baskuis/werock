<?php

$view = '
<p>Hi {{firstName}},</p>
<p>Please click the link below to reset your password:</p>
<p><a href="' . HTTP_PROTOCOL . DOMAIN_NAME . '/user/password/reset?key={{resetKey}}">reset my password</a></p>
<p>If you did not request to reset your password, please contact us at ' . SITE_EMAIL . '.</p>
';