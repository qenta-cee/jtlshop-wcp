<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageMpass extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::MPASS;

}