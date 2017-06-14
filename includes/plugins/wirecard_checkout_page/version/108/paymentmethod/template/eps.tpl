<div class="row hidden wcp_addon wcp_eps_ideal">
    <div class="form-group required">
        <label class="col-sm-2">{$txt_wcp_eps_ideal_bank_institution}</label>
        <div class="col-sm-4">
            <select class="form-control" name="wcp_{$method}_financial_institution">
                {foreach from=$wcp_{$method}_institutions key=key item=item}
                    <option value="{$key}">{$item}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>