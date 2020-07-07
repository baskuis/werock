(function($, window, document, undefined){
    $().ready(function(){
        $('.show-graphs').click(function(){
            $(this).toggleClass('active');
            $('.widget-column').toggleClass('show');
            $('.listings-column').toggleClass('compact');
            $(window).trigger('resize');
        });
    });
})(jQuery, window, document);