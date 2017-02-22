<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPagePayPal extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::PAYPAL;

}