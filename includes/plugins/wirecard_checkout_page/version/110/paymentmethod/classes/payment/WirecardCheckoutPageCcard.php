<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageCcard extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::CCARD;

}