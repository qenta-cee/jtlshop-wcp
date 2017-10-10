<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

</head>
<body>
<h3 style="color: #333333; font: 0.75em/1.3 Verdana,Helvetica,Arial,sans-serif; font-size: 1.1em; margin-top: 1.5em; font-weight: bold;"><label>{$txt_redirect}</label></h3>
<p style="color: #333333; font: 0.75em/1.3 Verdana,Helvetica,Arial,sans-serif">If not, please click <a href="#" onclick="iframeBreakout()">here</a></p>
<form method="POST" name="redirectForm" action="{$url}" target="_parent">
    <input type="hidden" name="redirected" value="1" />
	{foreach from=$args key="k" item="v"}
        <input type="hidden" name="{$k|escape}" value="{$v|escape}" />
    {/foreach}
</form>
<script type="text/javascript">
    // <![CDATA[
    function iframeBreakout()
    {ldelim}
        document.redirectForm.submit();
    {rdelim}

    iframeBreakout();
    //]]>
</script>
</body>
</html>
