<?php
namespace Test\Testpayment\Controller\Index;

class PaymentConfig extends \Magento\Framework\App\Action\Action
{

    protected $helperData;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Test\Testpayment\Helper\PaymentHelper $helperData
    )
    {
        $this->helperData = $helperData;
        return parent::__construct($context);
    }

    public function execute()
    {

    }

    public function getSystemInfo($fieldId)
    {
        return $this->helperData->getGeneralConfig($fieldId);//sort_order api_key
    }

    public function getClient($endpoint, $apiKey)
    {
        return $this->helperData->createClient($endpoint, $apiKey);
    }

    public function getIssuresJson($client)
    {
        return json_encode($this->helperData->getIssuers($client));
    }

    public function getDataArray($currency, $amount, $description, $merchantOrderId, $returnUrl, $paymentMethod, $issuerId)
    {
        return $this->helperData->collectDataForRequest($currency, $amount, $description, $merchantOrderId, $returnUrl, $paymentMethod, $issuerId);
    }
}
