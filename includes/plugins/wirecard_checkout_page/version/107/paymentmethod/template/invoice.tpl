{if $wcp_display_birthdate}
    <div class="row hidden wcp_addon wcp_birthdate">
        <div class="form-group required">
            <label class="col-sm-2">{lang key="birthday" section="account data"}</label>
            <div class="col-sm-3">
                <select class="form-control" name="wcp_birthday_day">
                    {foreach from=$wcp_days key=key item=item}
                        <option value="{$item}" {if $item==$wcp_selected_day}selected="selected"{/if}>{$item}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-sm-3">
                <select class="form-control" name="wcp_birthday_month">
                    {foreach from=$wcp_months key=key item=item}
                        <option value="{$item}" {if $item==$wcp_selected_month}selected="selected"{/if}>{$item}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-sm-3">
                <select class="form-control" name="wcp_birthday_year">
                    {foreach from=$wcp_years key=key item=item}
                        <option value="{$item}">{$item}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-sm-9 col-sm-offset-2 col-xs-12 hidden">
                <div class="alert alert-danger">{$txt_wcp_birthdate_invalid}</div>
            </div>
        </div>
    </div>
{/if}

{if $wcp_display_payolution_terms}
    <div class="row hidden wcp_addon wcp_payolution_terms">
        <div class="form-group">
            <div class="col-sm-2">
                <input onclick="if($(this).is(':checked'))$(this).closest('.form-group').find('.alert').parent().addClass('hidden');else $(this).closest('.form-group').find('.alert').parent().removeClass('hidden')" type="checkbox" name="wcp_{$method}_payolution_terms" id="wcp_{$method}_payolution_terms">
            </div>
            <div class="col-sm-9">
                <label style="padding-left:0" for="wcp_{$method}_payolution_terms">
                    {$txt_wcp_payolution_terms}
                </label>
            </div>
            <div class="col-sm-9 col-sm-offset-2 hidden">
                <div class="alert alert-danger">{$txt_wcp_payolution_error}</div>
            </div>
        </div>
    </div>
{/if}