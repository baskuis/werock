<?php

$view = <<<EOF
    <div class="modal personpicker" id="person_picker_{{name}}_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Pick Person</h4>
                </div>
                <div class="container">
                    <div class="searchBar">
                        <input type="text" id="person_picker_{{name}}_search" name="{{name}}_search" class="form-control" placeholder="{{placeholder}}" autocomplete="off" />
                    </div>
                </div>
                <div class="container">
                    <div class="container-fluid">
                        <div id="person_picker_{{name}}_target" class="row">
                            <!-- tiles -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                    <a href="#" data-dismiss="modal" class="btn btn-primary">Save changes</a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-control form-control-block">
        <span id="person_picker_{{name}}_selected">
            <!-- people load here -->
        </span>
        <a data-toggle="modal" href="#person_picker_{{name}}_modal" id="person_picker_{{name}}_modal_trigger"><span class="glyphicon glyphicon-plus"></span> Pick Person</a>
    </div>

    <input type="hidden" id="person_picker_{{name}}_field" name="{{name}}" value="{{default_value}}" />
EOF;

$script = <<<EOF
    $().ready(function(){
        $().personpicker({
            name : "{{name}}"
        });
    });
EOF;
