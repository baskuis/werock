;(function($, window, document, undefined){
    $().ready(function(){
        $('.uploadFile').click(function(){
            $(this).parent().find('input[type=file]').click();
        });
    });
})(jQuery, window, document);