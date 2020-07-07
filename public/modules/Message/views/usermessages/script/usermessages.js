$().ready(function(){

    $('#message_viewer').each(function(){

        /**
         * Load messages
         */
        WEv1api.setEndpoint('/messages').get(function(response){
            if(response.messages == undefined || response.messages.length == 0){
                return null;
            }
            for(i in response.messages){
                $('#werock_messages_list').append(WRTemplates.messagerow.render(response.messages[i]));
            }
        });

        /**
         * View a message
         */
        $('#werock_messages_list').on('click', '.message_row', function(){
            var message_data = $(this).data();
            WEv1api.setEndpoint('/message/' + message_data.id).get(function(response){
                if(response == undefined){
                    return null;
                }
                $('#message_viewer').html(WRTemplates.messageview.render(response));
            });
        });

    });

});