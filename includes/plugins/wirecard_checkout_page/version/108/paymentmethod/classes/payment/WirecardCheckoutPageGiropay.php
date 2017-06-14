<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageGiropay extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::GIROPAY;

}