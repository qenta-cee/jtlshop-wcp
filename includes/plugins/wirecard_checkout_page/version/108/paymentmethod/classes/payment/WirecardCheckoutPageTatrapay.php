<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageTatrapay extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::TATRAPAY;

}