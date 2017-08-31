<?php
/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Wirecard Central Eastern
 * Europe GmbH
 * (abbreviated to Wirecard CEE) and are explicitly not part of the Wirecard
 * CEE range of products and services.
 *
 * They have been tested and approved for full functionality in the standard
 * configuration
 * (status on delivery) of the corresponding shop system. They are under
 * General Public License Version 2 (GPLv2) and can be used, developed and
 * passed on to third parties under the same terms.
 *
 * However, Wirecard CEE does not provide any guarantee or accept any liability
 * for any errors occurring when used in an enhanced, customized shop system
 * configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and
 * requires a comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Wirecard CEE does not guarantee
 * their full functionality neither does Wirecard CEE assume liability for any
 * disadvantages related to the use of the plugins. Additionally, Wirecard CEE
 * does not guarantee the full functionality for customized shop systems or
 * installed plugins of other vendors of plugins within the same shop system.
 *
 * Customers are responsible for testing the plugin's functionality before
 * starting productive operation.
 *
 * By installing the plugin into the shop system the customer agrees to these
 * terms of use. Please do not use the plugin if you do not agree to these
 * terms of use!
 */
global $smarty, $customer;

ini_set('include_path', dirname(__FILE__) . '/../paymentmethod/classes/lib/' . PATH_SEPARATOR .ini_get('include_path'));
require_once "autoload.php";

$tmpl_path = dirname(__FILE__) . '/../paymentmethod/template/';

$step = Shop::Smarty()->getTemplateVars('step');

if (Shop::getPageType() == PAGE_BESTELLVORGANG && $step == 'Bestaetigung') {
    if (!isset($_SESSION['wcp_consumerDeviceId'])) {
        $_SESSION['wcp_consumerDeviceId'] = md5($oPlugin->oPluginEinstellungAssoc_arr['wirecard_checkout_page_customer_id'] . "_" . microtime());
    }

    $smarty->assign(array('consumerDeviceId' => $_SESSION['wcp_consumerDeviceId']));
    pq('footer')->append($smarty->fetch($tmpl_path . "wcp_consumerdeviceid.tpl"));
}

if (Shop::getPageType() === PAGE_BESTELLVORGANG && $step == 'Zahlung') {
    $translate = function ($key) use ($oPlugin) {
        return !array_key_exists($key,
            $oPlugin->oPluginSprachvariableAssoc_arr) ? $key : $oPlugin->oPluginSprachvariableAssoc_arr[$key];
    };

    $get_config = function ($key) use ($oPlugin) {
        return $oPlugin->oPluginEinstellungAssoc_arr[$key];
    };

    $customer = new Kunde($smarty->tpl_vars["Kunde"]->value->kKunde);
    $kPlugin = $oPlugin->kPlugin;

    /**
     * phpquery selectors for our payment methods
     */
    $selectors = array(
        'invoice' => "#kPlugin_{$kPlugin}_wirecardcheckoutpageinvoice",
        'installment' => "#kPlugin_{$kPlugin}_wirecardcheckoutpageinstallment",
        'eps' => "#kPlugin_{$kPlugin}_wirecardcheckoutpageepsonlinebanktransfer",
        'ideal' => "#kPlugin_{$kPlugin}_wirecardcheckoutpageideal"
    );

    $payolution_mid = $get_config('wirecard_checkout_page_payolution_mid');

    /** generate consent message replacing the _word_ with a link or word */
    $consent_message = preg_replace_callback(
        '/_\w+_/i',
        function ($match) use ($payolution_mid) {
            $match = str_replace("_", "", $match[0]);
            if (strlen($payolution_mid)) {
                return '<a href="https://payment.payolution.com/payolution-payment/infoport/dataprivacyconsent?mId=' . urlencode(base64_encode($payolution_mid)) . '" target="_blank">' . $match . '</a>';
            }
            return $match;
        },
        $translate("Wcp_payolution_terms"));

    $smarty_data = array(
        'plugin_id' => $kPlugin,
        'wcp_days' => range(1, 31, 1),
        'wcp_months' => range(1, 12, 1),
        'wcp_years' => array_reverse(
            range(
                intval(date('Y')) - 100,
                intval(date('Y')) - 17,
                1)),
        'tmpl_path' => $tmpl_path,
        'txt_wcp_birthdate_invalid' => $translate("Wcp_birthdate_under_18"),
        'txt_wcp_payolution_terms' => $consent_message,
        'txt_wcp_payolution_error' => $translate("Wcp_payolution_terms_not_checked"),
        'txt_wcp_eps_ideal_bank_institution' => $translate('Wcp_eps_ideal_bank_institution'),
        'wcp_eps_institutions' => WirecardCEE_Stdlib_PaymentTypeAbstract::getFinancialInstitutions(WirecardCEE_QPay_PaymentType::EPS),
        'wcp_ideal_institutions' => WirecardCEE_Stdlib_PaymentTypeAbstract::getFinancialInstitutions(WirecardCEE_QPay_PaymentType::IDL)
    );

    if (!strlen($customer->dGeburtstag) || $customer->dGeburtstag == '00.00.0000') {
        $smarty_data['wcp_display_birthdate'] = true;
    }

    try {
        $birthday = new DateTime($customer->dGeburtstag);
    } catch (Exception $e) {
        $smarty_data['wcp_display_birthdate'] = true;
    }

    $diff = $birthday->diff(new DateTime);
    $customerAge = $diff->format('%y');
    if ($customerAge < 18) {
        $birthdate_data = array(
            'wcp_display_birthdate' => true,
            'wcp_selected_day' => $birthday->format("d"),
            'wcp_selected_month' => $birthday->format("m")
        );
        $smarty_data = array_merge($smarty_data, $birthdate_data);
    }

    $smarty->assign($smarty_data);

    pq('head')->append($smarty->fetch($tmpl_path . "payment_scripts.tpl"));
    foreach ($selectors as $payment => $selector) {
        $template_path = $tmpl_path . $payment . ".tpl";
        if (pq($selector . " label")->length && file_exists($template_path)) {
            $smarty->assign('method', $payment);
            if( in_array($payment, array('invoice','installment'))) {
                $smarty->assign('wcp_display_payolution_terms',
                    $get_config('wirecard_checkout_page_' . $payment . '_provider') == 'payolution' && $get_config('wirecard_checkout_page_payolution_terms') == 1);
            }
            pq($selector . " label")->append($smarty->fetch($template_path));
        }
    }
}
