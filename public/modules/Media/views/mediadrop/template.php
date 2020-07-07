<?php

$view = <<<EOF
    <span id="media-drop-for-{{uniqueid}}">
        <ul class="uploadlist">
            <!-- instances of mediaqueueditem -->
        </ul>
        <div class="mediadrop">
            <div style="display: none;">
                <div id="fileReaderSWFObject"><!-- swf helper --></div>
            </div>
            <input type="file" class="file-upload" style="opacity: 0; position: absolute; top: -1000px;" accept="{{#extensions}}{{.}},{{/extensions}}" />
            <div class="uploadFile">
                <span class="label">Click to select file</span>
                <span class="subtext">Or drag and drop file{{^data.singular}}s{{/data.singular}} here</span>
            </div>
            <div class="spacer">
                <!-- spacer -->
            </div>
            <input type="hidden" name="mediadropinput_{{uniqueid}}" id="id_mediadropinput_{{uniqueid}}" />
        </div>
    </span>
EOF;

$script = <<<EOF
    ;(function($, windows, document, undefined){
        $().ready(function(){

            /**
             * If we have existing ids lets
             * get the details build build the correct ui
             */
            var existingIds = $("#media_drop_{{uniqueid}}_field").val();
            if(existingIds.length > 0){
                $.each(existingIds.split(","), function(index, value){
                    WEv1api.setEndpoint("/media/" + value).get(function(response){
                        if(response.media !== undefined){
                            $("#media_drop_{{uniqueid}}_selected").append(WRTemplates.forminputimagetile.render(response.media));
                            $(".uploadlist", "#media-drop-for-{{uniqueid}}").append("<li class=\\"mediaqueueditem\\">" + WRTemplates.mediaqueuepreview.render(response.media) + "</li>");
                        }
                    });
                });
            }

            /**
             * Handle remove click
             *
             */
            $(".uploadlist", "#media-drop-for-{{uniqueid}}").on("click", ".mediaqueueditem .remove", function(){
                $(this).parent().remove();
            });

            /**
             * FileReader options definition
             *
             * @type {{on: {load: load, error: error, groupstart: groupstart, groupend: groupend}}}
             */
            var opts = {

                on: {

                    /**
                     * File has been loaded into memory
                     * now it is ready to be sent to the remote server
                     */
                    load: function(e, file) {

                        /**
                         * WERock endpoint wrapper
                         * this allows the data to be sent to the remote server
                         * and will handle exceptions as they occur
                         * we are stripping the type data and sending that seperate
                         * the base64 encoded string will be decoded before stored
                         */
                        WEv1api.setEndpoint("/media/create").post({
                            fileName: file.name,
                            data: e.target.result.match(/,(.*)$/)[1],
                            type: e.target.result.match(/^data:([^;]*);/)[1],
                            uniqueID: file.extra.uniqueID
                        }, function(response){

                            /**
                             * Once the file has been uploaded
                             * lets replace the pending state with a
                             * preview state
                             */
                            $(".media-upload-" + response.uniqueID, "#media-drop-for-{{uniqueid}}").html(WRTemplates.mediaqueuepreview.render(response.media));

                        }, {

                            /**
                             * Wev1api allows the method to be passed
                             * and this will update the upload progress
                             */
                            uploadProgress: function(progress, payload){
                                $(".media-upload-" + payload.uniqueID + " .preview", "#media-drop-for-{{uniqueid}}").html(WRTemplates.mediaqueueprogress.render({percentage: (progress * 100)}));
                            }

                        });

                    },

                    /**
                     * Handle error
                     * TODO: fancier error handling
                     */
                    error: function(e, file) {
                        alert("error");
                    },

                    /**
                     * File(s) have been seletected
                     * lets show them in a pending state
                     */
                    groupstart: function(group) {

                        /**
                         * This shows the upload element in the active state when files are being
                         * selected/loaded
                         */
                        $(".mediadrop", "#media-drop-for-{{uniqueid}}").addClass("active");

                        if(typeof group.files !== "undefined"){

                            //{{#data.singular}}
                            /**
                             * We can only accept one file
                             * if there are more selected
                             * when using drag and drop
                             * they will be ignored
                             */
                            group.files = (typeof group.files[0] !== "undefined") ? [group.files[0]] : null;
                            //{{/data.singular}}

                            /**
                             * Append pending uploads to the upload list
                             * once they start uploading they will show progress
                             * and eventually be replaced with their preview state
                             */
                            $.each(group.files, function(index, file){
                                var markup = WRTemplates.mediaqueueditem.render(file);
                                $(".uploadlist", "#media-drop-for-{{uniqueid}}").append(markup);
                            });

                        }

                    },

                    /**
                     * Group of files has been introduced
                     */
                    groupend: function(group) {

                        /**
                         * Stop showing active state once the whole
                         * group has been loaded
                         */
                        $(".mediadrop", "#media-drop-for-{{uniqueid}}").removeClass("active");

                    }

                }
            };

            /**
             * Attach filereader handling to the following elements
             */
            $("input.file-upload", "#media-drop-for-{{uniqueid}}").fileReaderJS(opts);
            $(".uploadFile", "#media-drop-for-{{uniqueid}}").fileReaderJS(opts);

            /**
             * Handle save changes click
             * grab data element from preview
             * template - this is populated with the
             * response object from the /create endpoint
             */
            $("#media_drop_{{uniqueid}}_save").click(function(){

                /**
                 * Out with the old
                 */
                $("#media_drop_{{uniqueid}}_selected").html("");

                /**
                 * These are the values we will store
                 * simply the media ids should do
                 */
                var values = [];

                /**
                 * And in with the new
                 */
                $(".uploadlist .data", "#media-drop-for-{{uniqueid}}").each(function(){
                    var data = $(this).data();
                    $("#media_drop_{{uniqueid}}_selected").append(WRTemplates.forminputimagetile.render(data));
                    if(typeof data.id !== "undefined") values.push(data.id);
                });

                /**
                 * Finally set the media ids in the assigned form field
                 */
                $("#media_drop_{{uniqueid}}_field").val(values.join(","));

            });

        });
    })(jQuery, window, document);
EOF;
