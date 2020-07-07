<?php

$view = '
<li class="mediaqueueditem media-upload-{{extra.uniqueID}}" data-fileID="{{extra.fileID}}" data-groupID="{{extra.groupID}}" data-uniqueID="{{extra.uniqueID}}" data-type="{{type}}" data-name="{{name}}" data-size="{{size}}">
    <span class="icon glyphicon glyphicon-file"></span>
    <span class="name">{{name}}</span>
    <span class="size">{{extra.prettySize}}</span>
    <span class="remove glyphicon glyphicon-remove-circle"></span>
    <div class="preview">
        <div class="upload-media-loader"></div>
        <!-- preview loads here -->
    </div>
</li>
';

/**
{
    "webkitRelativePath":"",
    "lastModified":1415224350000,
    "lastModifiedDate":"2014-11-05T21:52:30.000Z",
    "name":"Screen Shot 2014-11-05 at 3.52.26 PM.png",
    "type":"image/png",
    "size":41378,
    "extra":
        {
                "nameNoExtension":"Screen Shot 2014-11-05 at 3.52.26 PM",
               "extension":"png",
            "fileID":0,
            "uniqueID":1,
            "groupID":1,
            "prettySize":"40.41 kb"}}
**/