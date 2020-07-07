<?php

$view = <<<EOF
<div id="{{typeaheadcontainerid}}_typeahead_holder" class="typeahead_options">
    <!-- options -->
</div>
EOF;

$script = <<<EOF
(function($, window, document, undefined){
    $().ready(function(){
        $().searchSuggestions({
            containerid : "{{typeaheadcontainerid}}",
            searchid : "{{typeaheadinputid}}",
            optiontemplate : "{{typeaheadoptiontemplate}}",
            urn : "{{urn}}",
            typeaheadid : "{{typeaheadcontainerid}}_typeahead_holder",
            autofocus : "{{autofocus}}"
        });
    });
})(jQuery, window, document);
EOF;
