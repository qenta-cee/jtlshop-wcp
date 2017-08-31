<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPagePaybox extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::PBX;

}