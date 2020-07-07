$().ready(function(){
	if(typeof CKEDITOR != undefined){
		$('.ckeditor').each(function(){
			CKEDITOR.replace($(this).prop('name'),{
                //customConfig: '/custom/ckeditor_config.js'
            });
		});
        $('.ckeditor_simple').each(function(){
            CKEDITOR.replace($(this).prop('name'));
        });
	}
});
