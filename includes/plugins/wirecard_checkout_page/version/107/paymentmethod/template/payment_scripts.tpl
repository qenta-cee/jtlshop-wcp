<script>
    $(document).ready(function(){
        $("*[id^=kPlugin_{$plugin_id}_wirecardcheckoutpage]").each(function(){
            var radio = $("input[name=Zahlungsart]", this);
            radio.change(function(){
                $("*[id^=kPlugin_{$plugin_id}_wirecardcheckoutpage] .wcs_addon").addClass("hidden");
                if($('.wcs_addon',$(this).parent()).length > 0){
                    $('.wcs_addon',$(this).parent()).removeClass("hidden");
                }
            });
        });
    });
</script>