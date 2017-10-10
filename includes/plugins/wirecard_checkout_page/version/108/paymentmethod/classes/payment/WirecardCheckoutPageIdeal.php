<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageIdeal extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::IDL;

}