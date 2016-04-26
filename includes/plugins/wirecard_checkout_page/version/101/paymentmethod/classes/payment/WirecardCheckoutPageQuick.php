<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageQuick extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::QUICK;

}