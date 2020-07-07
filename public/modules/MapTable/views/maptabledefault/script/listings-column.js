;(function($, window, document, undefined){
    $().ready(function(){
        function resizeColumns(){
            if($('body.admin').length == 0) return;
	        var baseHeight = $(window).height() - 1 - $('body.admin').css('padding-top').replace('px', '');
            $('.listings-column').css('height', baseHeight + 'px');
            $('.listings-column .listings-wrapper').css('height', baseHeight - $('.listing-header').height() + 'px');
            $('.form-column').css('height', baseHeight + 'px');
        }
        var timeout = null; resizeColumns(); $(window).resize(function(){ clearTimeout(timeout); timeout = setTimeout(resizeColumns, 200); });
    });
})(jQuery, window, document);