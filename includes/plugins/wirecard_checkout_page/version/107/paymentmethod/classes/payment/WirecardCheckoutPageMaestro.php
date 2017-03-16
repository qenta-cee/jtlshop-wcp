<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageMaestro extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::MAESTRO;

}