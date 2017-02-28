<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageSkrillDirect extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::SKRILLDIRECT;

}