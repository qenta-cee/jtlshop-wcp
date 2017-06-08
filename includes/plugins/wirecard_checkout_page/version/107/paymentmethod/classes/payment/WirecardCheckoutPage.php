<?php
/*
 * Shop System Plugins - Terms of use
 *
 * This terms of use regulates warranty and liability between Wirecard Central Eastern Europe (subsequently referred to as WDCEE) and it's
 * contractual partners (subsequently referred to as customer or customers) which are related to the use of plugins provided by WDCEE.
 *
 * The Plugin is provided by WDCEE free of charge for it's customers and must be used for the purpose of WDCEE's payment platform
 * integration only. It explicitly is not part of the general contract between WDCEE and it's customer. The plugin has successfully been tested
 * under specific circumstances which are defined as the shopsystem's standard configuration (vendor's delivery state). The Customer is
 * responsible for testing the plugin's functionality before putting it into production enviroment.
 * The customer uses the plugin at own risk. WDCEE does not guarantee it's full functionality neither does WDCEE assume liability for any
 * disadvantage related to the use of this plugin. By installing the plugin into the shopsystem the customer agrees to the terms of use.
 * Please do not use this plugin if you do not agree to the terms of use!
 */


ini_set('include_path', dirname(__FILE__) . '/../lib' . PATH_SEPARATOR .ini_get('include_path'));

require_once 'autoload.php';
require_once(PFAD_ROOT . PFAD_INCLUDES_MODULES . 'PaymentMethod.class.php');

define('WIRECARD_CHECKOUT_PAGE_INVOICE_INSTALLMENT_MIN_AGE', 18);
define('WIRECARD_CHECKOUT_PAGE_WINDOWNAME', 'WirecardCheckoutPageFrame');


class WirecardCheckoutPage extends PaymentMethod
{

    protected $paymenttype = WirecardCEE_QPay_PaymentType::SELECT;

    /**
     * Initialize payment object
     *
     * @param int $nAgainCheckout
     * @return $this|void
     */

    function init($nAgainCheckout = 0)
    {
        parent::init($nAgainCheckout);
        $this->name = 'WirecardCheckoutPage';
        $this->caption = 'WirecardCheckoutPage';
    }


    /**
     * Send data and redirect
     *
     * @global object $oPlugin
     *
     * @param Bestellung $order
     */
    public function preparePaymentProcess($order)
    {
        global $smarty;

        $redirectUrl = $this->initiatePayment($order);

        $smarty->assign('useIframe', $this->getConfig('use_iframe'));
        $smarty->assign('redirectUrl', $redirectUrl);
        $smarty->assign('windowName', WIRECARD_CHECKOUT_PAGE_WINDOWNAME);
    }

    /**
     * @param Bestellung $order
     *
     * @return WirecardCEE_QPay_Response_Initiation
     * @throws
     */
    protected function initiatePayment($order)
    {
        global $oPlugin;

        try {

            $client = new WirecardCEE_QPay_FrontendClient(array(
                                                               'CUSTOMER_ID' => $this->getConfig('customer_id'),
                                                               'SHOP_ID' => $this->getConfig('shop_id'),
                                                               'SECRET' => $this->getConfig('secret'),
                                                               'LANGUAGE' => convertISO2ISO639(
                                                                   $_SESSION['cISOSprache']
                                                               )
                                                          ));

            // consumer data (IP and User aget) are mandatory!
            $consumerData = new WirecardCEE_Stdlib_ConsumerData();
            $consumerData->setUserAgent($_SERVER['HTTP_USER_AGENT'])
                ->setIpAddress($_SERVER['REMOTE_ADDR']);

            if ($this->getConfig('send_additional_data') || in_array(
                    $this->paymenttype,
                    array(WirecardCEE_QPay_PaymentType::INVOICE, WirecardCEE_QPay_PaymentType::INSTALLMENT)
                )
            ) {
                $this->setConsumerInformation($order, $consumerData);
            }

            $version = WirecardCEE_QPay_FrontendClient::generatePluginVersion(
                $this->getVendor(),
                JTL_VERSION,
                $this->name,
                $oPlugin->nVersion
            );

            $amount = number_format($order->fGesamtsummeKundenwaehrung, 2, '.', '');
            $paymentHash = $this->generateHash($order);

            $client->setAmount($amount)
                ->setCurrency(strtolower($order->Waehrung->cISO))
                ->setPaymentType($this->paymenttype)
                ->setOrderDescription($this->getOrderDescription($order))
                ->setPluginVersion($version)
                ->setSuccessUrl($this->getNotificationURL($paymentHash))
                ->setPendingUrl($this->getNotificationURL($paymentHash))
                ->setCancelUrl($this->getNotificationURL($paymentHash))
                ->setFailureUrl($this->getNotificationURL($paymentHash))
                ->setConfirmUrl($this->getNotificationURL($paymentHash) . "&confirm=1")
                ->setServiceUrl($this->getConfig('service_url'))
                ->setImageUrl($this->getConfig('image_url'))
                ->setConsumerData($consumerData)
                ->setDisplayText($this->getConfig('display_text'))
                ->setCustomerStatement($this->getCustomerStatement($order))
                ->setDuplicateRequestCheck(false)
                ->setMaxRetries($this->getConfig('max_retries'))
                ->setAutoDeposit($this->getConfig('auto_deposit'))
                ->createConsumerMerchantCrmId($_SESSION['Kunde']->cMail)
                ->setWindowName(WIRECARD_CHECKOUT_PAGE_WINDOWNAME);

            if( $this->paymenttype == WirecardCEE_QMore_PaymentType::MASTERPASS )
                $client->setShippingProfile('NO_SHIPPING');

            if ($this->getConfig('send_basket')
                || ( $this->getConfig('installment_provider') != 'payolution' && $this->paymenttype == WirecardCEE_QMore_PaymentType::INSTALLMENT)
                || ( $this->getConfig('invoice_provider') != 'payolution' && $this->paymenttype == WirecardCEE_QMore_PaymentType::INVOICE)
            ) {
                $this->setBasket($client, $order);
            }

            $client->xIframeUsed = $this->getConfig('use_iframe');
            $client->xLanguage = convertISO2ISO639($_SESSION['cISOSprache']);

            $response = $client->initiate();

            $this->doLog(__METHOD__ . ':' . print_r($client->getRequestData(), true));

            if ($response->hasFailed()) {
                throw new Exception("Response failed! Error: {$response->getError()->getMessage()}");
            }
        } catch (Exception $e) {
            if ($this->duringCheckout) {
                $url = URL_SHOP . '/bestellvorgang.php?editZahlungsart=1';
            } else {
                $url = $order->BestellstatusURL;
            }

            $_SESSION['wirecard_checkout_page_message'] = $e->getMessage();

            header('Location: ' . $url);
            exit;
        }
        return $response->getRedirectUrl();
    }

    /**
     * @param Bestellung $order
     * @param string $paymentHash
     * @param array $args
     */
    function handleNotification($order, $paymentHash, $args)
    {
        $this->doLog(__METHOD__ . ':' . print_r($args, true));

        if (isset($args['confirm'])) {
            echo $this->confirmRequest($order, $paymentHash, $args);
            exit;
        }

        $url = $this->getReturnURL($order);

        /** @var $return WirecardCEE_Stdlib_Return_ReturnAbstract */
        $return = WirecardCEE_QPay_ReturnFactory::getInstance($args, $this->getConfig('secret'));
        if (! $return->validate()) {
            $message = $this->translate('Validation_Error_Invalid_Response');
            if ($this->duringCheckout) {
                $url = URL_SHOP . '/bestellvorgang.php?editZahlungsart=1';
            } else {
                //$url = URL_SHOP . '/bestellab_again.php?kBestellung=' . $order->kBestellung;
                //$url = URL_SHOP . '/jtl.php?bestellung=' . $order->kBestellung;
                $url = $order->BestellstatusURL;

            }
        }

        if ($return->getPaymentState() == WirecardCEE_QPay_ReturnFactory::STATE_FAILURE) {
            /** @var $return WirecardCEE_QPay_Return_Failure */
            $message = htmlentities($return->getErrors()->getConsumerMessage());
            if ($this->duringCheckout) {
                $url = URL_SHOP . '/bestellvorgang.php?editZahlungsart=1';
            } else {
                //$url = URL_SHOP . '/bestellab_again.php?kBestellung=' . $order->kBestellung;
                //$url = URL_SHOP . '/jtl.php?bestellung=' . $order->kBestellung;
                $url = $order->BestellstatusURL;
            }
        }

        if ($return->getPaymentState() == WirecardCEE_QPay_ReturnFactory::STATE_CANCEL) {
            /** @var $return WirecardCEE_QPay_Return_Failure */
            $message = $this->translate('Payment_Cancelled');
            if ($this->duringCheckout) {
                $url = URL_SHOP . '/bestellvorgang.php?editZahlungsart=1';
            } else {
                //$url = URL_SHOP . '/bestellab_again.php?kBestellung=' . $order->kBestellung;
                //$url = URL_SHOP . '/jtl.php?bestellung=' . $order->kBestellung;
                $url = $order->BestellstatusURL;
            }
        }

        $_SESSION['wirecard_checkout_page_message'] = $message;

		$this->doLog(__METHOD__ . ':redirecting to:' . $url);


		header('Location: ' . $url);
        exit;
    }

    /**
     * confirm request, server-to-server
     *
     * @param Bestellung $order
     * @param $paymentHash
     * @param $args
     *
     * @return string
     */
    function confirmRequest($order, $paymentHash, $args)
    {
        try {
            /** @var $return WirecardCEE_Stdlib_Return_ReturnAbstract */
            $return = WirecardCEE_QPay_ReturnFactory::getInstance($args, $this->getConfig('secret'));
            if (! $return->validate()) {
                $message = 'Validation error: invalid response';

                //$order->update_status('failed', $message);
                return WirecardCEE_QPay_ReturnFactory::generateConfirmResponseString($message);
            }


            switch ($return->getPaymentState()) {
                case WirecardCEE_QPay_ReturnFactory::STATE_SUCCESS:
                    /** @var $return WirecardCEE_QPay_Return_Success */
                    $incomingPayment = new stdClass();
                    $incomingPayment->fBetrag = $order->fGesamtsummeKundenwaehrung;
                    $incomingPayment->cISO = $order->Waehrung->cISO;
                    $sHinweis = "";
                    if($return instanceof WirecardCEE_QPay_Return_Success_Elv)
                    {

                        $sHinweis .= $return->getMandateId() != '' ? 'mandateId ' . $return->getMandateId() . ' ' : '';
                        $sHinweis .= $return->getMandateSignatureDate() != '' ? 'mandateSignatureDate ' . $return->getMandateSignatureDate() . ' ' : '';
                        $sHinweis .= $return->getCreditorId() != '' ? 'creditorId ' . $return->getCreditorId() . ' ' : '';
                        $sHinweis .= $return->getDueDate() != '' ? 'dueDate ' . $return->getDueDate() . ' ' : '';
                    }

                    $sHinweis .= sprintf('OrderNumber %s GatewayReferenceNumber %s',
                        $return->getOrderNumber(),
                        $return->getGatewayReferenceNumber()

                    );
                    $incomingPayment->cHinweis = $sHinweis;

                    $this->addIncomingPayment($order, $incomingPayment);
                    $this->setOrderStatusToPaid($order);
                    $this->sendConfirmationMail($order);

                    $this->updateNotificationID($order->kBestellung, $args['orderNumber']);
                    break;

                case WirecardCEE_QPay_ReturnFactory::STATE_PENDING:
                    /** @var $return WirecardCEE_QPay_Return_Pending */
                    $incomingPayment = new stdClass();
                    $incomingPayment->cHinweis = 'Pending';
                    $this->addIncomingPayment($order, $incomingPayment);
                    break;

                case WirecardCEE_QPay_ReturnFactory::STATE_CANCEL:
                    /** @var $return WirecardCEE_QPay_Return_Cancel */
                    //$this->cancelOrder($order->kBestellung);
                    break;

                case WirecardCEE_QPay_ReturnFactory::STATE_FAILURE:
                    /** @var $return WirecardCEE_QPay_Return_Failure */
                    //$this->cancelOrder($order->kBestellung);
                    break;

                default:
                    break;
            }
        } catch (Exception $e) {
            $this->doLog(__METHOD__ . ':' . $e->getMessage());
            $message = $e->getMessage();
        }

        $ret =  WirecardCEE_QPay_ReturnFactory::generateConfirmResponseString($message);
        $this->doLog(__METHOD__ . ":$ret");
        return $ret;
    }

    /**
     * @return boolean
     *
     * @param Bestellung $order
     * @param string $paymentHash
     * @param array $args
     */
    function verifyNotification($order, $paymentHash, $args)
    {
        /** @var $return WirecardCEE_Stdlib_Return_ReturnAbstract */
        $return = WirecardCEE_QPay_ReturnFactory::getInstance($args, $this->getConfig('secret'));
        return $return->validate();
    }

    /**
     * Finalizes order if everything is ok, clear cart and session data
     *
     * @param  Object $order         Current order
     * @param  Object $hash          Current order hash
     * @param  array $args          response arguments
     *
     * @return Bool
     */
    function finalizeOrder($order, $hash, $args)
    {
        $this->doLog(__METHOD__ . ':' . print_r($args, true));

        // make this here, because notify.php just echos the redirect url, when returning false, weird!?!!
        $url = URL_SHOP . '/bestellvorgang.php?editZahlungsart=1';

        /** @var $return WirecardCEE_Stdlib_Return_ReturnAbstract */
        $return = WirecardCEE_QPay_ReturnFactory::getInstance($args, $this->getConfig('secret'));
        if (! $return->validate()) {
            $_SESSION['wirecard_checkout_page_message'] = $this->translate('Validation_Error_Invalid_Response');
            header("Location: $url");
            die;
        }

        if ($return->getPaymentState() == WirecardCEE_QPay_ReturnFactory::STATE_FAILURE) {
            $_SESSION['wirecard_checkout_page_message'] = htmlentities($return->getErrors()->getConsumerMessage());
            header("Location: $url");
            die;
        }

        if ($return->getPaymentState() == WirecardCEE_QPay_ReturnFactory::STATE_CANCEL) {
            /** @var $return WirecardCEE_QPay_Return_Failure */
            $_SESSION['wirecard_checkout_page_message'] = $this->translate('Payment_Cancelled');
            header("Location: $url");
            die;
        }

        return true;
    }

    protected function getConfig($param)
    {
        global $oPlugin;

        $config = array(
            'demo' => array(
                'customer_id' => 'D200001',
                'shop_id' => '',
                'secret' => 'B8AKTPWBRMNBV455FG6M2DANE99WU2'
            ),
            'test' => array(
                'customer_id' => 'D200411',
                'shop_id' => '',
                'secret' => 'CHCSH7UGHVVX2P7EHDHSY4T2S4CGYK4QBE4M5YUUG2ND5BEZWNRZW5EJYVJQ'
            ),
            'test3d' => array(
                'customer_id' => 'D200411',
                'shop_id' => '3D',
                'secret' => 'DP4TMTPQQWFJW34647RM798E9A5X7E8ATP462Z4VGZK53YEJ3JWXS98B9P4F'
            )
        );

        if (in_array($param, array('customer_id', 'shop_id', 'secret'))) {
            if( $this->getConfig('configuration') != 'production' ){
                return $config[$this->getConfig('configuration')][$param];
            }
        }

		$param = "wirecard_checkout_page_$param";
        // when doing handleNotifcation we have no $oPlugin available
        if (! is_object($oPlugin)) {
            $oEinstellungen = $GLOBALS["DB"]->executeQuery(
                "SELECT cWert FROM tplugineinstellungen WHERE cName = '$param'",
                1
            );
            return $oEinstellungen->cWert;
        } else {
            return $oPlugin->oPluginEinstellungAssoc_arr[$param];
        }
    }


    protected function getVendor()
    {
        global $Einstellungen;
        return $Einstellungen['global']['global_shopname'];
    }

    /**
     * @param Bestellung $order
     *
     * @return string
     */
    protected function getOrderDescription($order)
    {
        /** @var Kunde $customer */
        $customer = $_SESSION['Kunde'];

        return sprintf('user_id:%s order_id:%s', $customer->kKunde, $order->cBestellNr);
    }

    /**
     * @param Bestellung $order
     *
     * @return string
     */
    protected function getCustomerStatement($order)
    {
        return sprintf('%s #%06s', $this->getVendor(), $order->cBestellNr);
    }

    /**
     * @param Bestellung $order
     * @param WirecardCEE_Stdlib_ConsumerData $consumerData
     */
    protected function setConsumerInformation($order, WirecardCEE_Stdlib_ConsumerData $consumerData)
    {
        /** @var Kunde $customer */
        $customer = $_SESSION['Kunde'];

        if (strlen($customer->dGeburtstag) && $customer->dGeburtstag != '00.00.0000') {
            $dt = null;
            try {
                $dt = new DateTime($customer->dGeburtstag);
            } catch (Exception $e) {

            }
            if ($dt !== null && $dt->format('y') > 0) {
                $consumerData->setBirthDate($dt);
            }
        }

        $consumerData->setEmail($customer->cMail);

        $billingAddress = new WirecardCEE_Stdlib_ConsumerData_Address(WirecardCEE_Stdlib_ConsumerData_Address::TYPE_BILLING);

        $countryCode = $order->oRechnungsadresse->cLand;

        $billingAddress->setFirstname(html_entity_decode($order->oRechnungsadresse->cVorname))
            ->setLastname(html_entity_decode($order->oRechnungsadresse->cNachname))
            ->setAddress1(html_entity_decode($order->oRechnungsadresse->cStrasse . ' ' . $order->oRechnungsadresse->cHausnummer))
            ->setCity(html_entity_decode($order->oRechnungsadresse->cOrt))
            ->setZipCode($order->oRechnungsadresse->cPLZ)
            ->setCountry($countryCode)
            ->setPhone($order->oRechnungsadresse->cTel);


        // not existend in jtlshop??
        //$billingAddress->setState($order->billing_state);

        $shippingAddress = new WirecardCEE_Stdlib_ConsumerData_Address(WirecardCEE_Stdlib_ConsumerData_Address::TYPE_SHIPPING);

        $src = is_object($order->Lieferadresse) ? $order->Lieferadresse : $order->oRechnungsadresse;
        $countryCode = $src->cLand;

        $shippingAddress->setFirstname(html_entity_decode($src->cVorname))
            ->setLastname(html_entity_decode($src->cNachname))
            ->setAddress1(html_entity_decode($src->cStrasse . ' ' . $order->Lieferadresse->cHausnummer))
            ->setCity(html_entity_decode($src->cOrt))
            ->setZipCode($src->cPLZ)
            ->setCountry($countryCode)
            ->setPhone($src->cTel);

        //$shippingAddress->setState($order->billing_state);

        $consumerData->addAddressInformation($billingAddress)
            ->addAddressInformation($shippingAddress);

    }

    /**
     * set basket information
     *
     * @param WirecardCEE_QPay_FrontendClient $client
     * @param Bestellung $order
     */
    protected function setBasket(WirecardCEE_QPay_FrontendClient &$client, Bestellung $order){
        $basket = new WirecardCEE_Stdlib_Basket();

        $currency = $order->Waehrung->cName;

        foreach($order->Positionen as $product){

            // set shipping
            if($product->cName == $order->cVersandartName){
                $basket_item = new WirecardCEE_Stdlib_Basket_Item('shipping');

                $basket_item->setName($order->cVersandartName);
                $basket_item->setDescription('Shipping');

                $gross_amount = number_format(floatval(str_replace(",",".",$product->cEinzelpreisLocalized[0][$currency])),2);
                $net_amount = number_format(floatval(str_replace(",",".",$product->cEinzelpreisLocalized[1][$currency])),2);

                $basket_item->setUnitGrossAmount($gross_amount);
                $basket_item->setUnitNetAmount($net_amount);
                $basket_item->setUnitTaxRate(number_format($product->fMwSt, 2));
                $basket_item->setUnitTaxAmount(number_format($gross_amount-$net_amount, 2));

                $basket->addItem($basket_item,1);
                continue;
            }
            $basket_item = new WirecardCEE_Stdlib_Basket_Item($product->kArtikel);

            // set name and description
            $basket_item->setName(substr($product->cName, 0, 127));
            if(strlen(@$product->Artikel->cKurzBeschreibung) > 1)
                $basket_item->setDescription(substr(@$product->Artikel->cKurzBeschreibung, 0, 127));
            else
                $basket_item->setDescription(substr($product->cName, 0, 127));

            // set prices
            $prices = @$product->Artikel->Preise;
            $basket_item->setUnitGrossAmount(number_format(@$prices->fVKBrutto,
                2));
            $basket_item->setUnitNetAmount(number_format(@$prices->fVKNetto, 2));
            $basket_item->setUnitTaxRate(number_format($product->fMwSt, 2));
            $basket_item->setUnitTaxAmount(number_format(@$prices->fVKBrutto - @$prices->fVKNetto,
                2));

            // set picture
            if ( count(@$product->Artikel->Bilder) > 0){
                $basket_item->setImageUrl( Shop()->getURL(true) . '/' . $product->Artikel->Bilder[0]->cPfadMini);
            }

            $basket->addItem($basket_item, $product->nAnzahl);
        }

        $client->setBasket($basket);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    protected function translate($key)
    {
        global $oPlugin;

        if (! array_key_exists($key, $oPlugin->oPluginSprachvariableAssoc_arr)) {
            return $key;
        }

        return $oPlugin->oPluginSprachvariableAssoc_arr[$key];
    }

    /**
     * @param Bestellung $order
     * @param Object $incomingPayment (Key, Zahlungsanbieter, Abgeholt, Zeit is set here)
     */
    function addIncomingPayment($order, $incomingPayment)
    {
        global $DB;

        $incomingPayment->kBestellung = $order->kBestellung;
        $incomingPayment->cZahlungsanbieter = $order->cZahlungsartName;
        $incomingPayment->cAbgeholt = 'N';
        $incomingPayment->dZeit = 'now()';
        $DB->insertRow('tzahlungseingang', $incomingPayment);
    }

}