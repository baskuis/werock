<?php

ob_start();
phpinfo();
$view = ob_get_contents();
ob_clean();
