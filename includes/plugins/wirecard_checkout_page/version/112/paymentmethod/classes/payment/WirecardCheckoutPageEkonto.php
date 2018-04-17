<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageEkonto extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::EKONTO;

}