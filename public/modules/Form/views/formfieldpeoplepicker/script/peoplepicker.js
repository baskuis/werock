;(function($, window, document, undefined){

    /**
     * Wrap in plugin to allow multiple instances
     *
     * @param options
     */
    $.fn.peoplepicker = function(options){

        //use unique name
        var name = (typeof options.name !== 'undefined') ? options.name : '';

        /**
         * Set focus on search box when modal opens/shows
         */
        $('#people_picker_' + name + '_modal_trigger').click(function(){
            setTimeout(function(){
                $('#people_picker_' + name + '_search').focus();
            }, 300);
        });

        /**
         * Show existing
         */
        var existing = $('#people_picker_' + name + '_field').val();
        if(typeof existing !== 'undefined') {
            if(existing.length > 0) {

                var selectedPeople = existing.split(',');
                var newValues = [];
                var promises = [];

                $.each(selectedPeople, function(index, id){
                    if(id.length > 0) {
                        promises.push((function(id){
                            var deferred = new $.Deferred();
                            WEv1api.setEndpoint('/people/' + id).get(function (response) {
                                if (typeof response.user !== 'undefined') {
                                    var person = response.user;
                                    if (person.id !== undefined) {
                                        deferred.resolve(person);
                                    }else{
                                        deferred.resolve(false);
                                    }
                                }else{
                                    deferred.resolve(false);
                                }
                            });
                            return deferred;
                        })(id));
                    }
                });
                $.when.apply($, promises).then(function() {
                    var objects = arguments;
                    $.each(objects, function(index, entry){
                       if(typeof entry === 'object'){

                           //new values
                           newValues.push(entry.id);

                           //add the people picker tile
                           $('#people_picker_' + name + '_selected').append(WRTemplates.formpeopletile.render(entry));

                       }
                    });
                    $('#people_picker_' + name + '_field').val(newValues.join(','));
                });

            }
        }

        /**
         * People picker click handler
         */
        $('#people_picker_' + name + '_target').on('click', '.person-tile', function(){

            if($(this).hasClass('selected')){

                //get the data
                var person = $(this).data();

                //modify existing string
                var existing = $('#people_picker_' + name + '_field').val();
                var selectedPeople = existing.split(',');

                //take it out of selected
                for(var i = 0; i < selectedPeople.length; i++){
                    if(selectedPeople[i] == person.id){
                        selectedPeople.splice(i, 1);
                    }
                }

                //update actual input field
                $('#people_picker_' + name + '_field').val(selectedPeople.join(','));

                $('#people_picker_' + name + '_selected .person-selection').each(function(){

                    var selected_person = $(this).data();

                    if(person.id == selected_person.id){
                        $(this).remove();
                    }

                });

                //remove selected
                $(this).removeClass('selected');

                //uncheck checkbox
                $(this).find('input').prop('checked', false);

            }else{

                //get the data
                var person = $(this).data();

                //modify existing string
                var existing = $('#people_picker_' + name + '_field').val();
                var selectedPeople = existing.split(',');
                if($.inArray(person.id + "", selectedPeople) == -1){

                    //add to selected people
                    selectedPeople.push(person.id);

                    //add the people picker tile
                    $('#people_picker_' + name + '_selected').append(WRTemplates.formpeopletile.render(person));

                }

                //update actual input field
                $('#people_picker_' + name + '_field').val(selectedPeople.join(','));

                //hide this tile
                $(this).addClass('selected');

                //check checkbox
                $(this).find('input').prop('checked', true);

            }

        });

        /**
         * Handle remove from selection
         */
        $('#people_picker_' + name + '_selected').on('click', '.person-selection', function(){

            //unmark selected in preview
            var targetData = $(this).data();
            $('#people_picker_' + name + '_target .person-tile').each(function(){
                var data = $(this).data();
                if(data.id == targetData.id){
                    $(this).removeClass('selected');
                }
            });

            //modify existing string
            var existing = $('#people_picker_' + name + '_field').val();
            var selectedPeople = existing.split(',');

            //take it out of selected
            for(var i = 0; i < selectedPeople.length; i++){
                if(selectedPeople[i] == targetData.id){
                    selectedPeople.splice(i, 1);
                }
            }

            //update actual input field
            $('#people_picker_' + name + '_field').val(selectedPeople.join(','));

            //remove the html element
            $(this).remove();

        });

        /**
         * People search
         */
        $('#people_picker_' + name + '_search').keyup(function(){

            //search query
            var q = $(this).val();

            //if query long
            if(q.length > 1){

                //make search request
                WEv1api.setEndpoint('/people/search').setParams({q : q, start : 0, limit : 20}).get(function(response){

                    //reset
                    $('#people_picker_' + name + '_target').html('');

                    //write people
                    if(response.users != undefined){

                        //get existing ids
                        var existing = $('#people_picker_' + name + '_field').val();
                        var selectedPeople = existing.split(',');

                        //build entries
                        for(u in response.users){
                            if($.inArray(response.users[u].id + "", selectedPeople) > -1){
                                response.users[u].selected = true;
                            }
                            $('#people_picker_' + name + '_target').append(WRTemplates.formpeoplerow.render(response.users[u]));
                        }

                    }

                });

            }else{

                //reset
                $('#people_picker_' + name + '_target').html('');

            }

        });

    };

})(jQuery, window, document);