angular.module("engagementApp", ["ui.bootstrap"]).controller("engagementAppController", function($scope, $q, $uibModal, $location, $window, $timeout) {

    $scope.title = null;
    $scope.message = null;
    $scope.tag = null;

    $scope.init = function(collectEmailObject) {
        if(typeof ga !== 'undefined') {
            ga('send', 'event', 'Collect Name Email', 'Loaded', 'Engagement');
        }
        $scope.title = (collectEmailObject.title != undefined) ? collectEmailObject.title : null;
        $scope.message = (collectEmailObject.message != undefined) ? collectEmailObject.message : null;
        $scope.tag = (collectEmailObject.tag != undefined) ? collectEmailObject.tag : null;
        var followListPrompted = Cookies.get('followListPrompted:' + $scope.tag);
        if(followListPrompted != 1) {
            $timeout(function () {
                if(jQuery('.tour-backdrop').length == 0) {
                    $scope.promptCollectNameEmail();
                }
            }, 5000);
        };
    };

    $scope.promptCollectNameEmail = function(){
        var modalInstance = $uibModal.open({
            animation: true,
            controller: 'collectEmailCalloutController',
            templateUrl: 'collectEmailCallout.html',
            size: 'constrained',
            resolve: {
                title: function () {
                    return $scope.title;
                },
                message: function () {
                    return $scope.message;
                },
                tag: function () {
                    return $scope.tag;
                }
            }
        });
        modalInstance.result.then(function() {

        }, function () {
            //ignore
        });
    };

}).controller("collectEmailCalloutController", function($scope, $rootScope, $q, $window, $timeout, $uibModalInstance, title, message, tag){

    $scope.title = title;
    $scope.message = message;
    $scope.tag = tag;
    $scope.ref = {
        name: null,
        email: null,
        tag: $scope.tag
    };

    $scope.busy = false;

    /**
     * Lock the screen
     *
     */
    $scope.lockScreen = function(){
        if($scope.busy === true) return;
        $scope.busy = true;
        $.msg({
            autoUnblock : false,
            content : "<i class=\"loading_animation fa fa-circle-o-notch fa-spin\"></i>"
        });
        $timeout(function(){
            $scope.unlockScreen();
        }, 10000);
    };

    /**
     * Unlock the screen
     *
     */
    $scope.unlockScreen = function(){
        $scope.busy = false;
        $.msg("unblock");
    };

    $scope.modalCancel = function(){
        Cookies.set('followListPrompted:' + $scope.tag, 1, { expires: 365 });
        if(typeof ga !== 'undefined') {
            ga('send', 'event', 'Collect Name Email', 'Closed', 'Engagement');
        }
        $uibModalInstance.dismiss('cancel');
    };

    $scope.receiveUpdates = function(){
        $scope.lockScreen();
        WEv1api.setEndpoint('/engagement/email/capture').post($scope.ref, function(response){
            if(response.captured != undefined && response.captured == 1){
                Cookies.set('followListPrompted:' + $scope.tag, 1, { expires: 365 });
                if(typeof ga !== 'undefined') {
                    ga('send', 'event', 'Collect Name Email', 'Captured', 'Engagement');
                }
                $scope.unlockScreen();
                $uibModalInstance.dismiss('cancel');
            }
        }, {
            suppressNotifications : false
        });
    };

});
angular.bootstrap(document.getElementById("engagementApp"), ['engagementApp']);