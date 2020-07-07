<?php

$view = '
<input name="{{name}}" type="text" class="form-control" value="{{default_value}}" placeholder="{{placeholder}}" {{#disabled}}disabled="disabled"{{/disabled}} autocapitalize="off" autocorrect="off" autocomplete="on" spellcheck="false" />
<span class="already_exists">
    Username already exists! Please pick something different
</span>
';

$script = '
{{^disabled}}
    ;(function($){
        var usernameExistsTO = null;   
        $("input[name=\'{{name}}\']").keyup(function(){
            var username = $(this).val();
            var that = $(this);
            $(that).parent().find(".already_exists").slideUp(100);
            clearTimeout(usernameExistsTO);
            usernameExistsTO = setTimeout(function(){
                WEv1api.setEndpoint("/people/usernameexists/" + encodeURIComponent(username)).get(function(response){
                    if(response.existingUser != undefined && response.existingUser == 1){
                        $(that).parent().find(".already_exists").slideDown(100);
                    }
                }, {
                    suppressNotifications : true
                });
            }, 200);
        });
    })(jQuery);
{{/disabled}}
';