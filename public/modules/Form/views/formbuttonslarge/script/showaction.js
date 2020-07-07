;(function($, window, document, undefined){
    $().ready(function(){
        $('.buttons button, .buttons a').click(function(){
            var data = $(this).data();
            if(typeof data.activeValue !== 'undefined'){
                $(this).text(data.activeValue);
            }
        });
    });
})(jQuery, window, document);