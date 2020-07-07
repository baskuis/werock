(function($, window, document, undefined){
    $().ready(function(){

        var storeGroupObjectEntitlement = function(option, objectUrn, entitlementUrn){
            var deferred = $.Deferred();
            try {
                WEv1api.setEndpoint('/entitlements/create').post(
                    {
                        group_id: option.id,
                        group_urn: option.urn,
                        object_urn: objectUrn,
                        entitlement_urn: entitlementUrn
                    },
                    function (response) {
                        deferred.resolve(response);
                    }
                );
            } catch(e){
                deferred.reject(e);
            }
            return deferred.promise();
        };

        var handleGroupObjectEntitlements = function(option, objectUrn, entitlementUrns){
            var deferred = $.Deferred();
            try {
                WEv1api.setEndpoint('/entitlements/delete').post(
                    {
                        group_id : option.id,
                        group_urn : option.urn,
                        object_urn : objectUrn
                    },
                    function(response){
                        var promises = [];
                        $.each(entitlementUrns, function(index, entitlementUrn){
                            promises.push(storeGroupObjectEntitlement(option, objectUrn, entitlementUrn));
                        });
                        $.when.apply($, promises).done(function(){
                            deferred.resolve({});
                        });
                    }
                );
            } catch(e){
                deferred.reject(e);
            }
            return deferred.promise();
        };

        /**
         * Handle save entitlements modal
         *
         */
        $('#object_entitlements_save').on('click', function(e){

            //close when ready
            e.stopPropagation();

            //get model
            var model = $('#object_entitlements_modal').data();

            //object urn
            var objectUrn = model.object;

            //group promises
            var promises = [];

            //submit checked
            $('#object_entitlements_modal_groups input.security_group_option').each(function(){

                //group
                var option = $(this).data();

                //entitlements
                var entitlementUrns = [];

                //entitlements
                $(this).parents('.group').find('.entitlements input[type=checkbox]:checked').each(function(){
                    entitlementUrns.push($(this).val());
                });

                //stack em up
                promises.push(handleGroupObjectEntitlements(option, objectUrn, entitlementUrns));

            });

            //show loader
            $('#object_entitlements_modal_groups').html(WRTemplates.widgetloading.render());

            //get them done
            $.when.apply($, promises).done(function(){

                //close the modal
                $('#object_entitlements_modal').modal('hide');

            });

        });

        var getGroupEntitlements = function(group, objectUrn){
            var deferred = $.Deferred();
            try {
                WEv1api.setEndpoint('/entitlements/bytype/maptable.object').setParams({
                    group_id : group.id,
                    group_urn : group.urn,
                    object_urn : objectUrn
                }).get(function(response) {
                    if (typeof response.entitlements !== 'undefined') {
                        group.entitlements = response.entitlements;
                        $.each(group.entitlements, function(index, entitlement){
                            if(typeof entitlement.isApplied !== 'undefined' && (entitlement.isApplied === true || entitlement.isApplied === "1")){
                                group.isApplied = true; //group is .. applied
                            }
                        });
                        deferred.resolve(group);
                    }
                });
            } catch(e){
                deferred.reject(e);
            }
            return deferred.promise();
        };

        var getAllGroups = function(objectUrn){
            var deferred = $.Deferred();
            try {
                WEv1api.setEndpoint('/groups').setParams().get(function(response) {
                    if (typeof response.groups !== 'undefined') {
                        if (response.groups) {
                            var promises = [];
                            var size = response.groups.length;
                            $.each(response.groups, function (index, group) {
                                if (index == 0) {
                                    group.first = true;
                                }
                                if (size == index + 1 - 0) {
                                    group.last = true;
                                }
                                promises.push(getGroupEntitlements(group, objectUrn));
                            });
                            $.when.apply($, promises).done(function(){
                                deferred.resolve(arguments);
                            });
                        }
                    }
                });
            } catch(e){
                deferred.reject(e);
            }
            return deferred.promise();
        };

        //listenen for modal interaction
        $('#object_entitlements_modal').on('shown.bs.modal', function() {

            //show loader
            $('#object_entitlements_modal_groups').html(WRTemplates.widgetloading.render());

            //get model
            var model = $('#object_entitlements_modal').data();

            //object urn
            var objectUrn = model.object;

            //get groups
            getAllGroups(objectUrn).then(function(groups){

                $('#object_entitlements_modal_groups').html('');

                $.each(groups, function(index, group){

                    /** render template */
                    var html = WRTemplates.usergrouplistingadmin.render(group);

                    /** show group */
                    $('#object_entitlements_modal_groups').append(html);

                });

                //On load
                $('.security_group_option', '#object_entitlements_modal_groups').each(function(){
                    if(this.checked){
                        $(this).parents('.group').find('.entitlements').show().find('input').each(function(){
                            var that = this;
                            if(that.checked) {
                                var data = $(this).data();
                                if (typeof data.childUrns !== 'undefined') {
                                    var urns = data.childUrns.split(',');
                                    $.each(urns, function (index, urn) {
                                        if (urn == '') return;
                                        $(that).parents('.entitlements').find('input[type=checkbox]').each(function () {
                                            if ($(this).val() == urn) {
                                                $(this).attr('disabled', true);
                                                this.checked = true;
                                            }
                                        });
                                    });
                                }
                            }
                        });
                    }
                });

                //On change parent
                $('.security_group_option', '#object_entitlements_modal_groups').change(function(){
                    if(this.checked){
                        $(this).parents('.group').find('.entitlements').show().find('input').each(function(){
                            this.checked = true;
                            var that = this;
                            if(that.checked) {
                                var data = $(this).data();
                                if (typeof data.childUrns !== 'undefined') {
                                    var urns = data.childUrns.split(',');
                                    $.each(urns, function (index, urn) {
                                        if (urn == '') return;
                                        $(that).parents('.entitlements').find('input[type=checkbox]').each(function () {
                                            if ($(this).val() == urn) {
                                                $(this).attr('disabled', true);
                                                this.checked = true;
                                            }
                                        });
                                    });
                                }
                            }
                        });
                    }else{
                        $(this).parents('.group').find('.entitlements').hide().find('input').each(function(){
                            this.checked = false;
                            $(this).attr('disabled', false);
                        });
                    }
                });

                //On change entitlement
                $('.entitlements input[type=checkbox]', '#object_entitlements_modal_groups').change(function(){
                    var that = this;
                    $(that).parents('.entitlements').find('input[type=checkbox]').attr('disabled', false);
                    $(that).parents('.entitlements').find('input[type=checkbox]').each(function() {
                        if(this.checked) {
                            var data = $(this).data();
                            if (typeof data.childUrns !== 'undefined') {
                                var urns = data.childUrns.split(',');
                                $.each(urns, function (index, urn) {
                                    if (urn == '') return;
                                    $(that).parents('.entitlements').find('input[type=checkbox]').each(function () {
                                        if ($(this).val() == urn) {
                                            $(this).attr('disabled', true);
                                            this.checked = true;
                                        }
                                    });
                                });
                            }
                        }
                    });
                    var anyChecked = false;
                    $(this).parents('.entitlements').find('input[type=checkbox]').each(function(){
                        var that = this;
                        if(this.checked) {
                            var data = $(this).data();
                            if (typeof data.childUrns !== 'undefined') {
                                var urns = data.childUrns.split(',');
                                $.each(urns, function (index, urn) {
                                    if (urn == '') return;
                                    $(that).parents('.entitlements').find('input[type=checkbox]').each(function () {
                                        if ($(this).val() == urn) {
                                            $(this).attr('disabled', true);
                                            this.checked = true;
                                        }
                                    });
                                });
                            }
                        }
                        if(this.checked === true){
                            anyChecked = true;
                        }
                    });
                    if(!anyChecked){
                        $(this).parents('.entitlements').hide().parents('.group').find('.security_group_option').attr('checked', false);
                    }
                });

            });

        });

    });
})(jQuery, window, document);