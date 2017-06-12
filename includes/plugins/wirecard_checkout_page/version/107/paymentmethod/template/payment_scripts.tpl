<script>
    $(document).ready(function () {
        $("*[id^=kPlugin_{$plugin_id}_wirecardcheckoutpage]").each(function () {
            var radio = $("input[name=Zahlungsart]", this);
            radio.change(function () {
                $("*[id^=kPlugin_{$plugin_id}_wirecardcheckoutpage] .wcp_addon").addClass("hidden");
                if ($('.wcp_addon', $(this).parent()).length > 0) {
                    $('.wcp_addon', $(this).parent()).removeClass("hidden");
                }
            });
        });

        $("form#zahlung").submit(function (evt) {
            var $payment_method = $(this).find("input[name=Zahlungsart]:checked").parent();
            if ($(".wcp_birthdate", $payment_method).length > 0) {
                if (getAge(
                                $("select[name=wcp_birthday_day]", $payment_method).val(),
                                $("select[name=wcp_birthday_month]", $payment_method).val(),
                                $("select[name=wcp_birthday_year]", $payment_method).val()
                        ) < 18) {
                    $(".wcp_birthdate.wcp_addon .form-group", $payment_method).addClass("has-error");
                    $(".wcp_birthdate.wcp_addon .alert", $payment_method).parent().removeClass("hidden");
                    return false;
                    evt.preventDefault();
                }
            }

            if ($(".wcp_payolution_terms", $payment_method).length > 0) {
                if(!$(".wcp_payolution_terms input[type=checkbox]", $payment_method).is(":checked")){

                    $(".wcp_payolution_terms .form-group .alert", $payment_method).parent().removeClass("hidden");
                    return false;
                    evt.preventDefault();
                }
            }
        });

        $("form#zahlung .wcp_birthdate select").each(function () {
            $(this).change(function () {
                $(this).closest(".form-group").removeClass("has-error").find(".alert").parent().addClass("hidden");
            });
        });
    });

    // d m y
    function getAge(a, b, c) {
        var d = new Date(),
                g = new Date(c, b, a),
                age = d.getFullYear() - g.getFullYear(),
                f = d.getMonth() - g.getMonth();
        if (f < 0 || ( f === 0 && d.getDate() > g.getDate()))
            age--;
        return age;
    }
</script>