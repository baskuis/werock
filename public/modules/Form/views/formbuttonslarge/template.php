<?php

$view = '
<div class="buttons">
    <span class="xs-container">
        {{#data.isNew}}<button name="{{name}}_and_new" class="btn btn-lg btn-primary save-and-new" type="submit" data-active-value="Saving...">{{placeholder}} &amp; New</button>{{/data.isNew}}
        {{#data.isDelete}}<button name="{{name}}" class="btn btn-lg btn-danger submit save" type="submit" data-active-value="Deleting...">Delete</button>{{/data.isDelete}}
        {{^data.isDelete}}<button name="{{name}}" class="btn btn-lg btn-primary submit save" type="submit" data-active-value="Saving...">{{placeholder}}</button>{{/data.isDelete}}
    </span>
    <span class="xs-container">
        <a href="' . CoreArrayUtils::getString(array(), array('action' => '', 'primary_value' => '')) . '" class="btn btn-lg btn-default cancel" data-active-value="Cancelling...">Cancel</a>
        <button type="reset" class="btn btn-lg btn-default reset">Reset</button>
    </span>
</div>';