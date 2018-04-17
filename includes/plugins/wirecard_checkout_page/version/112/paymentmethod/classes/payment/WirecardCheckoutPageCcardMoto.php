<?php

if (!class_exists('WirecardCheckoutPage'))
    require 'WirecardCheckoutPage.php';


class WirecardCheckoutPageCcardMoto extends WirecardCheckoutPage
{
    protected $paymenttype = WirecardCEE_QPay_PaymentType::CCARD_MOTO;

}