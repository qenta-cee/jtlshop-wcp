<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageMoneta extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::MONETA;

}