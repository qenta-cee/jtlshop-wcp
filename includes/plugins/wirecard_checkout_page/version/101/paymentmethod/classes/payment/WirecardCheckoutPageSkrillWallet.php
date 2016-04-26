<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageSkrillWallet extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::SKRILLWALLET;

}