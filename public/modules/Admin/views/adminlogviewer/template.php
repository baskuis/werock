<?php

$view = '
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>{{title}}</h1>
            <pre class="logViewer">
                ' . CoreLog::readLog(1000) . '
            </pre>
        </div>
    </div>
</div>';