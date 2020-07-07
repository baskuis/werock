;(function($, document, window, undefined){
    $().ready(function(){
        if(typeof $.fn.mCustomScrollbar !== 'undefined') {
            if($("#admin-nav-scrollable-wrapper").length > 0) {
                $("#admin-nav-scrollable-wrapper").mCustomScrollbar({
                    theme: "minimal",
                    scrollInertia: 60
                });
            }
        }
    });
})(jQuery, document, window);