<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageP24 extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::P24;

}