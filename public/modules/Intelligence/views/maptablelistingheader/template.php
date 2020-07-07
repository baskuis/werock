<?php

$view = preg_replace('/\<\/div\>$/i', '
    <span class="show-graphs">
        <i class="fa fa-bar-chart"></i>
    </span>
</div>', $view);