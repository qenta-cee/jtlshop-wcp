<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPagePaysafecard extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::PSC;

}