;(function($, window, document, undefined){
    $.fn.searchSuggestions = function(options){

        //get settings
        var containerid = (typeof options.containerid !== 'undefined') ? options.containerid : '';
        var searchid = (typeof options.searchid !== 'undefined') ? options.searchid : '';
        var optiontemplate = (typeof options.optiontemplate !== 'undefined') ? options.optiontemplate : '';
        var typeaheadid = (typeof options.typeaheadid !== 'undefined') ? options.typeaheadid : '';
        var urn = (typeof options.urn !== 'undefined') ? options.urn : '';
        var autofocus = (typeof options.autofocus !== 'undefined' && options.autofocus == '1') ? true : false;

        //number of suggestions
        var max = 0;

        //set container height
        var containerHeight;
        function resolveContainerHeight() {
            containerHeight = $('#' + containerid).outerHeight();
            $('#' + typeaheadid).css({top: (containerHeight - 2) + 'px'});
        }
        resolveContainerHeight();

        //the index
        var currentIndex = -1;

        if(autofocus) {
            setTimeout(function () {
                $('#' + searchid).focus();
            }, 250);
        }

        function reset(){
            max = 0;
            currentIndex = -1;
        }
        function decrement(){
            currentIndex = ((currentIndex - 1) < -1) ? max - 1 : currentIndex - 1;
        }
        function increment(){
            currentIndex = (currentIndex + 1 >= max) ? -1 : currentIndex + 1;
        }
        function highlight(){
            $('#' + typeaheadid + ' .option').removeClass('active');
            if(currentIndex == -1){
                $('#' + searchid).focus();
                var data = $('#' + searchid).data();
                if(typeof data.original !== 'undefined'){
                    $('#' + searchid).val(data.original);
                }
            }else {
                $('#' + typeaheadid + ' .option:eq(' + currentIndex + ')').addClass('active');
                var selection = $('#' + typeaheadid + ' .option:eq(' + currentIndex + ')').text();
                $('#' + searchid).val(selection);
            }
            $('#' + searchid).trigger('input');
        }

        // show generic suggestions
        var typeAheadTimeout = null;
        $(document).on('focus', '#' + searchid, function(){
            resolveContainerHeight();
            if(max > 0) $('#' + typeaheadid).show();
            if($('#' + searchid).val() == '') {
                performSuggestions("");
            }
        }).on('blur', '#' + searchid, function(){
            setTimeout(function(){
                $('#' + typeaheadid).hide();
            }, 150);
        }).on('keyup', '#' + searchid, function(e){
            var that = this;
            clearTimeout(typeAheadTimeout);
            typeAheadTimeout = setTimeout(function(){
                switch (e.keyCode) {

                    case 37: //left
                        decrement();
                        highlight();
                        break;

                    case 38: //top
                        decrement();
                        highlight();
                        break;

                    case 39: //right
                        increment();
                        highlight();
                        break;

                    case 40: //bottom
                        increment();
                        highlight();
                        break;

                    case 13: //return
                        return;
                        break;

                    default: //something else
                        var text = $(that).val();
                        if (text.length > 1) {
                            performSuggestions(text);
                        }else{
                            $('#' + typeaheadid).html('').hide();
                            reset();
                        }
                        break;
                }
            }, 150);
        });

        var typeAheadRequest = null;
        var performSuggestions = function(text){
            $('#' + searchid).data('original', text);
            if(typeAheadRequest) typeAheadRequest.abort();
            typeAheadRequest = WEv1api.setEndpoint('/searchSuggestions/' + encodeURIComponent(urn) + '/' + encodeURIComponent(text)).get(function (response) {
                if (typeof response.suggestions !== 'undefined') {
                    var typeahead = '';
                    if(response.suggestions.length > 0) {
                        max = response.suggestions.length;
                        $.each(response.suggestions, function (index, suggestion) {
                            suggestion.index = index;
                            typeahead += WRTemplates[optiontemplate].render(suggestion);
                        });
                        $('#' + typeaheadid).html(typeahead).show();
                    }else{
                        $('#' + typeaheadid).html(typeahead).hide();
                    }
                }
            });
        };

    };
})(jQuery, window, document);