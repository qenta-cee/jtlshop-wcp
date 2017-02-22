<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPagePoli extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::POLI;

}