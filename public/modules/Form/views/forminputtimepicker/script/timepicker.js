$().ready(function(){
    if($().timepicker){
        $('.timepicker').timepicker({
            minuteStep: 15
        });
    }
});