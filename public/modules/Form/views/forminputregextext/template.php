<?php $view = '
<div class="regex_field">
    <div class="modal regex_field_modal" id="test_regex_{{name}}_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title"><i class="fa fa-cog"></i> Regex Tester</h4>
                </div>
                <div class="container-noop">
                    <div class="container-noop">
                        <div class="row-noop">
                            <div class="regexContainer">
                                <label>Regex</label>
                                <input class="form-control" name="test_regex_{{uniqueid}}_test_regex" />
                            </div>
                        </div>
                        <div class="row-noop">
                            <div class="inputContainer">
                                <label>Input</label>
                                <textarea class="form-control" name="test_regex_{{uniqueid}}_test_data"></textarea>
                            </div>
                        </div>
                        <div class="row-noop">
                            <div class="resultContainer">
                                <label>Results</label>
                                <pre id="test_regex_{{uniqueid}}_result"></pre>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                    <a href="#" class="btn btn-primary" id="test_regex_{{uniqueid}}_test">Test</a>
                    <a href="#" class="btn btn-primary" id="test_regex_{{uniqueid}}_save">Save</a>
                </div>
            </div>
        </div>
    </div>
    <input name="{{name}}" type="text" class="form-control" value="{{default_value}}" placeholder="{{placeholder}}" {{#disabled}}disabled="disabled"{{/disabled}} />
    <!-- TODO: Provide method to test regex -->
    <a data-toggle="modal" href="#test_regex_{{name}}_modal" class="text_regex_link">Test regex</a>
</div>
';

$script = '
(function($, window, document, undefined){
    $(function(){
        $("input[name=test_regex_{{uniqueid}}_test_regex]").val($("input[name={{name}}]").val());
        $("#test_regex_{{uniqueid}}_test").click(function(){
            var regex = $("input[name={{name}}]").val();
            var content = $("textarea[name=test_regex_{{uniqueid}}_test_data").val();
            WEv1api.setEndpoint("/form/regex/tester").post({
                regex: regex,
                content: content
            }, function (response) {
                $("#test_regex_{{uniqueid}}_result").text(JSON.stringify(response.matches, null, 2));
            }, {
                suppressNotifications: false
            });
        });
        $("#test_regex_{{uniqueid}}_save").click(function(){
            $("input[name={{name}}]").val($("input[name=test_regex_{{uniqueid}}_test_regex]").val());
        });
    });
})(jQuery, window, document);
';