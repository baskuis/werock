<?php

/** @var EngagementCollectEmailObject $EngagementCollectEmailObject */
$EngagementCollectEmailObject = isset($data[0]) ? $data[0] : null;

$view = '
<div ng-app="engagementApp" id="engagementApp" ng-controller="engagementAppController" ng-init="init(' . CoreStringUtils::objectToEncodedAttributeString($EngagementCollectEmailObject) . ')">
    <script type="text/ng-template" id="collectEmailCallout.html">
        <div class="modal-header">
            <button class="btn btn-default pull-right" type="button" ng-click="modalCancel()">Close</button>
            <h3 class="modal-title">{{title}}</h3>
        </div>
        <div class="modal-body modal-body-engage" ng-init="init()">
            <form class="engageForm" novalidate ng-cloak>
                <p class="lead">{{message}}</p>
                <div class="form-group">
                    <label>Receive Updates?</label>
                    <div class="onoffswitch">
                        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" ng-model="ref.receiveUpdates" />
                        <label class="onoffswitch-label" for="myonoffswitch">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                    <p>Would you like to receive updates?</p>
                </div>
                <div class="form-group" ng-show="ref.receiveUpdates" ng-cloak>
                    <label>Your Name? <strong>*</strong></label>
                    <input type="text" ng-model="ref.name" class="form-control" required placeholder="ie: Sarah" />
                    <p>Please tell us your name.</p>
                </div>
                <div class="form-group" ng-show="ref.receiveUpdates" ng-cloak>
                    <label>Your Email? <strong>*</strong></label>
                    <input type="text" ng-model="ref.email" class="form-control" required placeholder="ie: sarah@email.com" />
                    <p>Where we can send you updates?</p>
                </div>
                <div class="form-group text-right" ng-show="ref.receiveUpdates" ng-cloak>
                    <button type="button" class="btn btn-lg" ng-click="modalCancel()">Cancel</button>
                    <button type="button" class="btn btn-success btn-lg" ng-click="receiveUpdates()">Save</button>
                </div>
            </form>
        </div>
    </script>
</div>
';