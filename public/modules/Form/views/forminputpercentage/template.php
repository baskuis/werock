<?php

$view = <<<EOF
    <div id="{{uniqueid}}" class="form-control percentage-picker">
        <input class="percentage" type="text" name="{{name}}" value="{{default_value}}" />
        <span class="percentage-sign">%</span>
        <div class="slider" style="margin-left: 50px;"><!-- slider --></div>
    </div>
EOF;

$script = <<<EOF
    $().ready(function(){
        $("input[name={{name}}]", "#{{uniqueid}}").keyup(function(){
            var this_slider_value = $(this).val().replace(/[^0-9]/, "");
            $(".slider", "#{{uniqueid}}").slider({ value: this_slider_value });
        });
        $(".slider", "#{{uniqueid}}").slider({
            value: "{{default_value}}",
            animate: true,
            change : function(event, ui){
                $("input[name={{name}}]", "#{{uniqueid}}").val(ui.value);
            }
        });
    });
EOF;
