;(function($, window, document, undefined){
    $().ready(function(){
        $('.maptable_associated_table').each(function(){

            // handle open close
            $(this).find('.control').click(function(){
                $(this).toggleClass('active').parent().find('.body').toggleClass('open');
            });

            // use custom scrollbar
            $(this).find('.body').mCustomScrollbar({
                theme : "minimal-dark",
                scrollInertia : 60
            });

        });
    });
})(jQuery, window, document);