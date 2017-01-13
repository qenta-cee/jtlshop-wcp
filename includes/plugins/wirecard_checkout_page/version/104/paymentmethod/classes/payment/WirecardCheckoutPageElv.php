<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageElv extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::ELV;

}