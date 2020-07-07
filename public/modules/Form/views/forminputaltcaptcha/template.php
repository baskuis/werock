<?php

$view = '
<input type="hidden" name="{{name}}" id="alt_{{name}}_id" placeholder="{{placeholder}}" value="{{value}}" class="form-control" />';

$script = '
    (function($, window, document, undefined){
        $(function(){
            var keyProvided = false;
            $("input,textarea").focus(function(){
                if(keyProvided) return; keyProvided = true;
                var key = \'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx\'.replace(/[xy]/g, function(c) { var r = Math.random() * 16 | 0, v = c == \'x\' ? r : (r&0x3|0x8); return v.toString(16); });
                $.ajax({ url: "/api/form/altcaptcha/key", type: "POST", data: { key : key, name : "{{name}}" }, success : function(response){ if(response.response != undefined && response.response.ok != undefined){ $("#alt_{{name}}_id").val(key); } } });
            });
        });
    })(jQuery, window, document);
';