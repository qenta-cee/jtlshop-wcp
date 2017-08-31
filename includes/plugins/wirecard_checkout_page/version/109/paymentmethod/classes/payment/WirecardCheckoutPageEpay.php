<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageEpay extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::EPAYBG;

}