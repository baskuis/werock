;(function($, window, undefined) {
    $(function(){
        var lastConnectivityStatus = window.connectivityActive;
        setInterval(function(){
            var show = false;
            /** window.connectivityRetrying is set by the apiV1.js file in the UserModule */
            /** window.connectivityRetrying = true means there is no connectivity */
            if(window.connectivityRetrying != undefined && window.connectivityRetrying === true){
                $('#apiConnectivityIndicator').find('.message').text('Unable to connect, retrying...'); show = true;
            }
            /** window.connectivityFailure is set by the apiV1.js file in the UserModule */
            /** window.connectivityFailure = true means there is no connectivity and retries have been exhausted */
            if(window.connectivityFailure != undefined && window.connectivityFailure === true){
                $('#apiConnectivityIndicator').find('.message').html('Something is wrong. Please <a href="">refresh this page</a>'); show = true;
            }
            /** window.connectivityActive is set by the apiV1.js file in the UserModule */
            /** window.connectivityActive = true means there is connectivity in process */
            if(window.connectivityActive != undefined && window.connectivityActive === true && lastConnectivityStatus === true){
                $('#apiConnectivityIndicator').find('.message').text('Connecting...'); show = true;
            }
            if(show){
                if($('#apiConnectivityIndicator').is(":hidden")) $('#apiConnectivityIndicator').show();
            }else{
                if(!$('#apiConnectivityIndicator').is(":hidden")) $('#apiConnectivityIndicator').hide();
            }
            lastConnectivityStatus = window.connectivityActive;
        }, 750);
    });
})(jQuery, window);