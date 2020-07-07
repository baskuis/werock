$().ready(function(){
    $('.notifications_block .close').on('click', function(){
       $(this).parent().hide();
    });
});