$().ready(function(){
    if(typeof Mousetrap !== 'undefined') {
        Mousetrap.bind('f', function (e) {
            $('#admin-left-nav-search input[name=admin_menu_search]').focus().val('');
        });
    }
});