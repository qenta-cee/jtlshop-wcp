<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageBmc extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::BMC;

}