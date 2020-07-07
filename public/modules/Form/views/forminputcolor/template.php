<?php

$view = <<<EOF
    <div class="color_picker_container" id="{{uniqueid}}">
        <input name="{{name}}" type="text" value="{{default_value}}" autocomplete="off" class="form-control color_picker_field" style="border-top-left-radius: 5px;border-top-right-radius: 5px;border-bottom-right-radius: 5px;border-bottom-left-radius: 5px;" {{#disabled}}disabled="disabled"{{/disabled}} />
        <div class="color_picker_canvas" style="display: none; margin-top: 5px;"><!-- picker --></div>
    </div>
EOF;

$script = <<<EOF
    {{^disabled}}
        $().ready(function(){

            /**
             * Apply the color picker
             */
            $(".color_picker_canvas", "#{{uniqueid}}").farbtastic("#{{uniqueid}} .color_picker_field");

            /**
             * Only show when field is in focus
             */
            $(".color_picker_field", "#{{uniqueid}}").focus(function(){
                $(".color_picker_canvas", "#{{uniqueid}}").show();
            });
            $(".color_picker_field", "#{{uniqueid}}").blur(function(){
                $(".color_picker_canvas", "#{{uniqueid}}").hide();
            });

        });
    {{/disabled}}
EOF;
