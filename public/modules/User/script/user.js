(function($, window, document, undefined){
    if(typeof window.user === 'undefined') window.user = {};
    window.user.lastPageview = new Date().getTime();
})(jQuery, window, document)