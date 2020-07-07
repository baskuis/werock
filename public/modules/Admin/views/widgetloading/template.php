<?php

$view = <<<OEF
<div class="loading show_dynamic_loading">
    ...
</div>
<script type="text/javascript">
    $().ready(function(){
        $(".show_dynamic_loading:not(.active)").showLoading();
    });
</script>
OEF;
