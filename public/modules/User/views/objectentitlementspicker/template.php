<?php

/** @var array $requires require additional data */
$requires = array('MapTableContextObject');

/** @var MapTableContextObject $MapTableContextObject */
$MapTableContextObject = $data['MapTableContextObject'];

$view = '
<div class="modal" id="object_entitlements_modal" data-object="maptable.object.' . $MapTableContextObject->getMapTableTableObject()->getName() . '">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Who can access?</h4>
            </div>
            <div id="object_entitlements_modal_groups">
                <!-- groups -->
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn">Close</a>
                <a href="#" id="object_entitlements_save" data-dismiss="modal" class="btn btn-primary">Save changes</a>
            </div>
        </div>
    </div>
</div>

';