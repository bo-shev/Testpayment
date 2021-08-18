<?php

namespace Test\Testpayment\Helper;

use Codeception\Lib\ModuleContainer;
use Magento\Framework\App\Helper\Context;
use Magento\FunctionalTestingFramework\Helper\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Ginger\Ginger;

class PaymentHelper extends AbstractHelper
{
    private $client;

    public function __construct(Context $context)
    {
        parent::__construct($context);
        require_once __DIR__ ."/../vendor/autoload.php";
    }

    const XML_PATH_TESTPAYMENT = 'payment/';

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_TESTPAYMENT .'testpayment/'. $code, $storeId);
    }

    public function createClient($endpoint, $apiKey)
    {
        $this->client = Ginger::createClient($endpoint, $apiKey);
        return $this->client;
    }

    public function getIssuers($client)
    {
        return $client->getIdealIssuers();
    }

    public function collectDataForRequest($currency, $amount, $description, $merchantOrderId, $returnUrl, $paymentMethod, $issuerId)
    {
        $paymentMethodDetails = array(
            "issuer_id" => $issuerId
        );

        $transactions = array(
            "payment_method" => $paymentMethod,
            "payment_method_details" => $paymentMethodDetails
        );

        $dataArray = array(
            "currency" => $currency,
            "amount" => $amount,
            "description" => $description,
            "merchant_order_id" => $merchantOrderId,
            "return_url" => $returnUrl,
            "transactions" => $transactions
        );

        return $dataArray;
    }

}
