<?php


if ($_REQUEST['xIframeUsed'] && ! array_key_exists('redirected', $_REQUEST)
    && ! array_key_exists('confirm', $_REQUEST)
) {

    require_once(PFAD_ROOT .'includes' . DIRECTORY_SEPARATOR . 'smartyInclude.php');

    $cPh = verifyGPDataString('ph');

    $cSh = verifyGPDataString('sh');

    if (strlen($cPh)) {
        $key = 'ph';
        $hash = $cPh;
    } else {
        $key = 'sh';
        $hash = $cSh;
    }

    $tmpl = dirname(__FILE__) . '/../paymentmethod/template/iframebreakout.tpl';
    $smarty->assign('url', gibShopURL() . '/includes/modules/notify.php?' . $key . '=' . $hash);
    $smarty->assign('args', $_REQUEST);
    if ($_REQUEST['language'] == 'de') {
        $smarty->assign('txt_redirect', 'Sie werden in K&uuml;rze weitergeleitet.');
        $smarty->assign(
            'txt_redirect_click',
            'falls nicht, klicken Sie bitte <a href="#" onclick="iframeBreakout()">hierl</a>'
        );

    } else {
        $smarty->assign('txt_redirect', 'You will be redirected shortly.');
        $smarty->assign('txt_redirect_click', 'If not, please click <a href="#" onclick="iframeBreakout()">here</a>');
    }

    $smarty->display($tmpl);

    exit;
}
