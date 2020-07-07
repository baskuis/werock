<?php

$view = '
    <div class="modal" id="media_drop_{{name}}_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Upload Media</h4>
                </div>
                <div class="container-noop">
                    <div class="container-noop">
                        <div class="row-noop">
                           ' . CoreTemplate::getView('mediadrop') . '
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                    <a href="#" data-dismiss="modal" class="btn btn-primary" id="media_drop_{{uniqueid}}_save">Save changes</a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-control form-control-block expandable">
        <span id="media_drop_{{uniqueid}}_selected">
            <!-- people load here -->
        </span>
        <a data-toggle="modal" href="#media_drop_{{name}}_modal" class="add-link"><span class="glyphicon glyphicon-plus"></span> Add {{label}}</a>
    </div>
    <input type="hidden" id="media_drop_{{uniqueid}}_field" name="{{name}}" value="{{default_value}}" />
';