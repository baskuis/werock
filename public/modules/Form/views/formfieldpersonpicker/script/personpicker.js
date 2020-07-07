;(function($, window, document, undefined){

    /**
     * Wrap in plugin to allow multiple instances
     *
     * @param options
     */
    $.fn.personpicker = function(options){

        //use unique name
        var name = (typeof options.name !== 'undefined') ? options.name : '';

        /**
         * Set focus on search box when modal opens/shows
         */
        $('#person_picker_' + name + '_modal_trigger').click(function(){
            setTimeout(function(){
                $('#person_picker_' + name + '_search').focus();
            }, 200);
        });

        /**
         * Show existing
         */
        var existingID = $('#person_picker_' + name + '_field').val();
        if(typeof existingID !== 'undefined') {
            if (existingID.length > 0) {
                WEv1api.setEndpoint('/people/' + existingID).get(function (response) {
                    if(typeof response.user !== 'undefined'){
                        var person = response.user;
                        $('#person_picker_' + name + '_selected').html(WRTemplates.formpeopletile.render(person));
                    }
                });
            }
        }

        /**
         * People picker click handler
         */
        $('#person_picker_' + name + '_target').on('click', '.person-tile', function(){

            var person = $(this).data();

            if($(this).hasClass('selected')){

                //modify existing string
                $('#person_picker_' + name + '_field').val('');

                //remove from preview
                $('#person_picker_' + name + '_selected .person-selection').remove();

                //remove selected
                $(this).removeClass('selected');

                //uncheck checkbox
                $(this).find('input').prop('checked', false);

            }else{

                //add the people picker tile
                $('#person_picker_' + name + '_selected').html(WRTemplates.formpeopletile.render(person));

                //modify existing string
                $('#person_picker_' + name + '_field').val(person.id);

                //hide this tile
                $(this).addClass('selected');

                //check checkbox
                $(this).find('input').prop('checked', true);

            }

        });

        /**
         * Handle remove from selection
         */
        $('#person_picker_' + name + '_selected').on('click', '.person-selection', function(){

            //unmark selected in preview
            var targetData = $(this).data();
            $('#person_picker_' + name + '_target .person-tile').each(function(){
                var data = $(this).data();
                if(data.id == targetData.id){
                    $(this).removeClass('selected');
                }
            });

            //modify existing string
            var existing = $('#person_picker_' + name + '_field').val('');

            //remove the html element
            $(this).remove();

        });

        /**
         * People search
         */
        var currentBrowseIdx = 0;
        $('#person_picker_' + name + '_search').keyup(function(e){

            var length = 0;
            if($('#person_picker_' + name + '_target .person-tile')){
                length = $('#person_picker_' + name + '_target .person-tile').length;
            }
            switch (e.keyCode){
                case 37: //left
                    currentBrowseIdx--;
                    if(currentBrowseIdx < 0) currentBrowseIdx = length - 1;
                    $('#person_picker_' + name + '_target .person-tile:eq(' + currentBrowseIdx + ') input').focus();
                    e.stopPropagation();
                    return;
                    break;
                case 38: //up
                    currentBrowseIdx--;
                    if(currentBrowseIdx < 0) currentBrowseIdx = length - 1;
                    $('#person_picker_' + name + '_target .person-tile:eq(' + currentBrowseIdx + ') input').focus();
                    e.stopPropagation();
                    return;
                    break;
                case 39: //right
                    currentBrowseIdx++;
                    if(currentBrowseIdx >= length) currentBrowseIdx = 0;
                    $('#person_picker_' + name + '_target .person-tile:eq(' + currentBrowseIdx + ') input').focus();
                    e.stopPropagation();
                    return;
                    break;
                case 40: //down
                    currentBrowseIdx++;
                    if(currentBrowseIdx >= length) currentBrowseIdx = 0;
                    $('#person_picker_' + name + '_target .person-tile:eq(' + currentBrowseIdx + ') input').focus();
                    e.stopPropagation();
                    return;
                    break;
                case 13: //return
                    $('#person_picker_' + name + '_target .person-tile:eq(' + currentBrowseIdx + ') input').trigger('click');
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                    break;
            }

            //search query
            var q = $(this).val();

            //if query long
            if(q.length > 1){

                //make search request
                WEv1api.setEndpoint('/people/search').setParams({q : q, start : 0, limit : 20}).get(function(response){

                    //reset
                    $('#person_picker_' + name + '_target').html('');

                    //write people
                    if(response.users != undefined){

                        //get existing ids
                        var existing = $('#person_picker_' + name + '_field').val();
                        var selectedPeople = existing.split(',');

                        //build entries
                        for(u in response.users){
                            if($.inArray(response.users[u].id + "", selectedPeople) > -1){
                                response.users[u].selected = true;
                            }
                            $('#person_picker_' + name + '_target').append(WRTemplates.formpeoplerow.render(response.users[u]));
                        }

                    }

                });

            }else{

                //reset
                $('#person_picker_' + name + '_target').html('');

            }

        });

    };

})(jQuery, window, document);