<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageMasterpass extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::MASTERPASS;

}