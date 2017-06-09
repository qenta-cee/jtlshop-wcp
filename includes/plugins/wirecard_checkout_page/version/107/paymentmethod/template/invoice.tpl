{if $display_birthdate}
    <div class="row hidden wcs_addon">
        <div class="form-group">
            <label class="col-sm-2">{lang key="birthday" section="account data"}</label>
            <div class="col-sm-3">
                <select class="form-control" name="birthday_day"></select>
            </div>
            <div class="col-sm-3">
                <select class="form-control" name="birthday_month"></select>
            </div>
            <div class="col-sm-3">
                <select class="form-control" name="birthday_year"></select>
            </div>
        </div>
    </div>
{/if}