<?php
$view = '
<div class="col-xs-6 person-tile {{#selected}}selected{{/selected}}" data-id="{{id}}" data-username="{{username}}">
    <div class="tile form-control">
        <input type="checkbox" {{#selected}}checked="checked"{{/selected}} />
        <a href="javascript:void(0);"><span class="glyphicon glyphicon-user"></span> {{username}}</a><br />
    </div>
</div>';