;(function($, document, window, undefined){
    $().ready(function(){
        $('#nav-toggle').click(function(){
            $(this).toggleClass('active');
            $('.left-menu').toggleClass('opened');
            $('.left-menu .search-wrapper').toggle();
            $('.left-menu-control .search-wrapper').toggle();
        });
    });
})(jQuery, document, window);