<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageClick2pay extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::C2P;

}