<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageSepaDD extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::SEPADD;

}