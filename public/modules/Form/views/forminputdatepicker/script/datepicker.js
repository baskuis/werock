$().ready(function(){
    if($().datepicker){
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd"
        });
    }
});