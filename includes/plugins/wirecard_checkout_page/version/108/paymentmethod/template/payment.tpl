{if $useIframe}
<iframe src="{$redirectUrl}" width="100%" height="820" name="{$windowName}" border="0" frameborder="0"></iframe>
{else}
<script type="text/javascript">
    $(document).ready(function() {ldelim}
        $('#wirecard_checkout_page_redirect').submit();
        {rdelim});
</script>

<div style="margin:10px 0;">
    <div>
        <form method="post" action="{$redirectUrl}" id="wirecard_checkout_page_redirect"></form>
    </div>
</div>

{/if}