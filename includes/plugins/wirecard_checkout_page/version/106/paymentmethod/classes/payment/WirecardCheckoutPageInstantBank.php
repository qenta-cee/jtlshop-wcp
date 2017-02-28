<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageInstantBank extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::INSTANTBANK;

}