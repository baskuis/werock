
/**
 * Core V1 Api
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
;var WEv1api = (function($, window, undefined){

    window.connectivityFailure = false;
    window.connectivityRetrying = false;
    window.connectivityActive = false;

    //The actual plugin constructor
    function Plugin(){
        this.defaults = {
            base: "/api/v1"
        };
        this.options = {};
    }

    //Plugin prototype
    Plugin.prototype = {

        //set endpoint
        setEndpoint : function(endpoint){
            this.endpoint = endpoint;
            return this;
        },

        //set parameters
        setParams : function(params){
            this.options.params = params;
            return this;
        },

        //do get
        get : function(callback, options){
            window.connectivityActive = true;
            var self = this;
            return $.ajax({
                url: this.defaults.base + this.endpoint,
                type: 'GET',
                data: this.options.params,
                dataType: 'json',
                success: function(response){
                    window.connectivityRetrying = false;
                    window.connectivityFailure = false;
                    if(
                        typeof response.notifications !== 'undefined' &&
                        (
                            (
                                typeof options !== 'undefined' &&
                                (typeof options.suppressNotifications === 'undefined' || options.suppressNotifications === false)
                            ) ||
                            typeof options === 'undefined'
                        )
                    ){
                        self.handleNotifications(response.notifications);
                    }
                    callback(response);
                },
                timeout: 15000,
                tryCount : 0,
                retryLimit : 50,
                complete : function() {
                    window.connectivityActive = false;
                },
                error : function(xhr, textStatus, errorThrown ) {
                    window.connectivityRetrying = (this.tryCount > 0);
                    if (textStatus == 'timeout' || xhr.status < 100) {
                        console.log('retrying request');
                        this.tryCount++;
                        if (this.tryCount <= this.retryLimit) {
                            //try again
                            var that = this;
                            setTimeout(function(){
                                $.ajax(that);
                            }, 3000);
                            return;
                        }
                        window.connectivityFailure = true;
                        return;
                    }
                    if (xhr.status == 500) {
                        self.handleError(errorThrown);
                    } else {
                        self.handleError(errorThrown);
                    }
                }
            });
        },

        //do put
        put : function(payload, callback, options){
            var self = this;
            return $.ajax({
                url: this.defaults.base + this.endpoint,
                type: 'PUT',
                data: payload,
                dataType: 'json',
                xhr: function() {

                    if(typeof window.XMLHttpRequest === 'undefined') return new window.XMLHttpRequest();
                    if(typeof options === 'undefined') return new window.XMLHttpRequest();

                    var xhr = new window.XMLHttpRequest();

                    //Upload progress
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            if(typeof options.uploadProgress === 'function'){
                                options.uploadProgress(percentComplete, payload);
                            }
                        }
                    }, false);

                    //Download progress
                    xhr.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            if(typeof options.downloadProgress === 'function'){
                                options.downloadProgress(percentComplete, payload);
                            }
                        }
                    }, false);

                    return xhr;

                },
                success: function(response){
                    window.connectivityRetrying = false;
                    window.connectivityFailure = false;
                    if(
                        typeof response.notifications !== 'undefined' &&
                        (
                            (
                                typeof options !== 'undefined' &&
                                (typeof options.suppressNotifications === 'undefined' || options.suppressNotifications === false)
                            ) ||
                            typeof options === 'undefined'
                        )
                    ){
                        self.handleNotifications(response.notifications);
                    }
                    callback(response);
                },
                timeout: 15000,
                tryCount : 0,
                retryLimit : 50,
                complete : function() {
                    window.connectivityActive = false;
                },
                error : function(xhr, textStatus, errorThrown ) {
                    window.connectivityRetrying = (this.tryCount > 0);
                    if (textStatus == 'timeout' || xhr.status < 100) {
                        console.log('retrying request');
                        this.tryCount++;
                        if (this.tryCount <= this.retryLimit) {
                            //try again
                            var that = this;
                            setTimeout(function(){
                                $.ajax(that);
                            }, 3000);
                            return;
                        }
                        window.connectivityFailure = true;
                        return;
                    }
                    if (xhr.status == 500) {
                        self.handleError(errorThrown);
                    } else {
                        self.handleError(errorThrown);
                    }
                }
            });
        },

        //do post
        post : function(payload, callback, options){
            var self = this;
            return $.ajax({
                url: this.defaults.base + this.endpoint,
                type: 'POST',
                data: payload,
                dataType: 'json',
                xhr: function() {

                    if(typeof window.XMLHttpRequest === 'undefined') return new window.XMLHttpRequest();
                    if(typeof options === 'undefined') return new window.XMLHttpRequest();

                    var xhr = new window.XMLHttpRequest();

                    //Upload progress
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            if(typeof options.uploadProgress === 'function'){
                                options.uploadProgress(percentComplete, payload);
                            }
                        }
                    }, false);

                    //Download progress
                    xhr.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            if(typeof options.downloadProgress === 'function'){
                                options.downloadProgress(percentComplete, payload);
                            }
                        }
                    }, false);

                    return xhr;

                },
                success: function(response){
                    window.connectivityRetrying = false;
                    window.connectivityFailure = false;
                    if(
                        typeof response.notifications !== 'undefined' &&
                        (
                            (
                                typeof options !== 'undefined' &&
                                (typeof options.suppressNotifications === 'undefined' || options.suppressNotifications === false)
                            ) ||
                            typeof options === 'undefined'
                        )
                    ){
                        self.handleNotifications(response.notifications);
                    }
                    callback(response);
                },
                timeout: 15000,
                tryCount : 0,
                retryLimit : 50,
                complete : function() {
                    window.connectivityActive = false;
                },
                error : function(xhr, textStatus, errorThrown ) {
                    window.connectivityRetrying = (this.tryCount > 0);
                    if (textStatus == 'timeout' || xhr.status < 100) {
                        console.log('retrying request');
                        this.tryCount++;
                        if (this.tryCount <= this.retryLimit) {
                            //try again
                            var that = this;
                            setTimeout(function(){
                                $.ajax(that);
                            }, 3000);
                            return;
                        }
                        window.connectivityFailure = true;
                        return;
                    }
                    if (xhr.status == 500) {
                        self.handleError(errorThrown);
                    } else {
                        self.handleError(errorThrown);
                    }
                }
            });
        },

        //do delete
        delete : function(payload, callback, options){
            var self = this;
            return $.ajax({
                url: this.defaults.base + this.endpoint,
                type: 'DELETE',
                data: payload,
                dataType: 'json',
                success: function(response){
                    window.connectivityRetrying = false;
                    window.connectivityFailure = false;
                    if(
                        typeof response.notifications !== 'undefined' &&
                        (
                            (
                                typeof options !== 'undefined' &&
                                (typeof options.suppressNotifications === 'undefined' || options.suppressNotifications === false)
                            ) ||
                            typeof options === 'undefined'
                        )
                    ){
                        self.handleNotifications(response.notifications);
                    }
                    callback(response);
                },
                timeout: 15000,
                tryCount : 0,
                retryLimit : 50,
                complete : function() {
                    window.connectivityActive = false;
                },
                error : function(xhr, textStatus, errorThrown ) {
                    window.connectivityRetrying = (this.tryCount > 0);
                    if (textStatus == 'timeout' || xhr.status < 100) {
                        console.log('retrying request');
                        this.tryCount++;
                        if (this.tryCount <= this.retryLimit) {
                            //try again
                            var that = this;
                            setTimeout(function(){
                                $.ajax(that);
                            }, 3000);
                            return;
                        }
                        window.connectivityFailure = true;
                        return;
                    }
                    if (xhr.status == 500) {
                        self.handleError(errorThrown);
                    } else {
                        self.handleError(errorThrown);
                    }
                }
            });
        },

        /**
         * Render notifications
         *
         * @param notifications
         */
        handleNotifications : function(notifications){
            try {
                if (
                    notifications.have__success ||
                    notifications.have__warning ||
                    notifications.have__error ||
                    notifications.have__standard
                ) {
                    $('#overlay_notifications').remove();
                    $('body').append(WRTemplates.apinotifications.render(notifications));
                }
            } catch(e){
                console.log('Unable to render notifications block', e);
            }
        },

        /**
         * Handle error
         *
         * @param error
         */
        handleError : function(error){
            if(typeof console !== 'undefined'){
                if(typeof console.log !== 'undefined'){
                    console.log('error', error);
                }
            }
        }

    }

    /**
     * Return plugin prototype
     *
     * @param options
     * @returns {Object|Function|Plugin}
     */
    return new Plugin();

})(jQuery, window);
