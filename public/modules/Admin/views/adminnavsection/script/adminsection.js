;(function($){
    $().ready(function(){

        /**
         * Handling open/close
         */
        $('.nav-section-link').click(function(){
            if($(this).hasClass('open')){
                closeSection($(this).parent());
            }else{
                openSection($(this).parent());
            }
        });

        /**
         * Close Section Helper
         */
        var closeSection = function(section){
            $(section).children('.admin-nav-section-items').hide();
            $(section).find('.glyphicon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
            $(section).removeClass('open').addClass('closed').find('.nav-section-link').removeClass('open').addClass('closed');
        }

        /**
         * Open Section Helper
         */
        var openSection = function(section){
            $(section).children('.admin-nav-section-items').show();
            $(section).find('.glyphicon').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
            $(section).removeClass('closed').addClass('open').find('.nav-section-link').removeClass('closed').addClass('open');
        }

        /**
         * Handle search
         */
        $('#admin-left-nav-search input').keyup(function(){
            searchForSubItems($(this).val());
        });

        /**
         * Handle clear search
         */
        $('#admin-left-nav-search .clear-search').click(function(){
            $('#admin-left-nav-search input').val('');
            searchForSubItems('');
        });

        /**
         * Search for sub items
         */
        var searchForSubItems = function(search){
            $('.admin-nav-section-items').each(function(){
                var sectionObject = $(this);
                if(search.length > 0){
                    var foundOne = false;
                    $(this).find('a').each(function(){
                        if($(this).text().toLowerCase().search(search.toLowerCase()) > -1){
                            $(this).parent().show();
                            foundOne = true;
                        }else{
                            $(this).parent().hide();
                        }
                    });
                    if(foundOne){
                        $(sectionObject).show();
                        $(sectionObject).find('.glyphicon').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
                    }else{
                        $(sectionObject).hide();
                        $(sectionObject).find('.glyphicon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
                    }
                }else{
                    if(!$(this).parent().hasClass('closed')){
                        openSection($(sectionObject).parent());
                    }else{
                        closeSection($(sectionObject).parent());
                    }
                    $('.admin-nav-section-items > li').show();
                }
            });
        }

        /**
         * Handle active state
         */
        $('.admin-nav-section-items li a').click(function(){
            $.blockUI({
                message : $('#the-loader')
            });
            $('.admin-nav-section-items li').removeClass('active');
            $(this).parent().addClass('active');
        });

    });
})(jQuery);