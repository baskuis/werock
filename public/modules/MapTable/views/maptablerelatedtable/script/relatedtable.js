;(function($, window, document, undefined){
    $().ready(function(){
        $('.maptable_related_table').each(function(){
            $(this).find('.control').click(function(){
                $(this).toggleClass('active').parent().find('.body').toggleClass('open');
            });
            $(this).find('.body iframe').iframeAutoHeight({
                animate : false
            });
        });
    });
})(jQuery, window, document);