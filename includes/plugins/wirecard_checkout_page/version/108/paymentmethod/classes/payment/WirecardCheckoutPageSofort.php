<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageSofort extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::SOFORTUEBERWEISUNG;

}