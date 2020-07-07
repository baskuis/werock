$().ready(function(){
    $('.modal').on('shown.bs.modal', function(){
        $('body').append($(this));
        $('.admin-container').addClass('add-blur');
    });
    $('.modal').on('hidden.bs.modal', function (){
        $('.admin-container').removeClass('add-blur');
    });
});