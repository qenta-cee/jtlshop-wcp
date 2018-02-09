<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageEps extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::EPS;

}