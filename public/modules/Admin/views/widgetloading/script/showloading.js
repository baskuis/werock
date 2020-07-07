(function($, window, document, undefined){
    var inveralTime = 200;
    var passes = 3;
    $.fn.showLoading = function(){
        var originalText = '...';
        var pass = 1;
        var appendString = '';
        var object = $(this);
        $(object).addClass('active');
        setInterval(function(){
            if(pass > passes){
                pass = 0;
                appendString = '';
            }else{
                appendString += '.';
            }
            $(object).text(appendString + originalText + appendString);
            pass++;
        }, inveralTime);
    }
})(jQuery, window, document);