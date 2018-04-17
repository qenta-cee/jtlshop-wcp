<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageTrustly extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::TRUSTLY;

}