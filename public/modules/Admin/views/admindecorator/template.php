<?php

CoreRender::addBodyClass('admin');

$view = '
<div class="admin-container">
    <div class="left-menu-control">
        <div class="container">
            <a id="nav-toggle"><span></span></a>
        </div>
        ' . CoreTemplate::getView('adminnavsearch') . '
    </div>
    <div class="left-menu">
        ' . CoreTemplate::getView('adminnav') . '
    </div>
    <div class="content-area">
        {{& decorator_content}}
    </div>
</div>
';