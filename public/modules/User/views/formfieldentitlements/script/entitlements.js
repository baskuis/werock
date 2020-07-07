;(function($, window, document, undefined){
    $().ready(function(){
        $('.form_entitlements').each(function(){

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

        $('.form_entitlements_field').each(function(){

            var outer = this;
            var formEntitlementsString = $(outer).val();
            var formEntitlements = formEntitlementsString.split(',');

            $(outer).parents('.form_entitlements').find('.option input').each(function(){
                var that = this;
                var checked = false;
                $.each(formEntitlements, function(index, entitlement){
                    if($(that).attr('name') == entitlement){
                        checked = true;
                    }
                });
                that.checked = checked;
            });

            $(outer).parents('.form_entitlements').find('.option input').change(function(){
                var selectedEntitlements = [];
                $(outer).parents('.form_entitlements').find('.option input').each(function(){
                     if(this.checked) {
                         selectedEntitlements.push($(this).attr('name'));
                     }
                });
                $(outer).val(selectedEntitlements.join(','));
            });

        });

    });
})(jQuery, window, document);