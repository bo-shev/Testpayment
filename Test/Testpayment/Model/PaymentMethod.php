<?php
namespace Test\Testpayment\Model;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Payment;
use mysql_xdevapi\Exception;
use Test\Testpayment\Helper\MessageManager;
use Test\Testpayment\Controller\Index\PaymentConfig;
use  \Magento\Payment\Model\Method\AbstractMethod;
use Testhelper\Customhelper\Model\DataHelper;

class PaymentMethod extends AbstractMethod
{

    const CODE = 'testpayment';

    protected $_code = self::CODE;

    protected $_canAuthorize = true; // Give ability redirect to success page!
    protected $_isGateway = true;
    protected $_isOffline = false;
    protected $_isInitializeNeeded = true;

    protected $_checkoutSession;
    protected $_product;
    protected $objectManager;
    protected $total;

    private $currentOrder;
    private $product;
    private $helper;
    private $myConfig;
    private $client;
    private $messageManager;

     private $endpoint = 'https://api.online.emspay.eu';

     protected $_canCapture = true;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null,
        \Magento\Framework\App\Action\Context $configContext,
        \Test\Testpayment\Helper\PaymentHelper $helperData,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Block\Onepage\Success $success,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Catalog\Model\Product $product
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
        );

       // $this->myConfig = new PaymentConfig($configContext, $helperData);
        $this->helper = $helperData;
        $this->messageManager = new MessageManager($messageManager);
        $this->_checkoutSession = $checkoutSession;
        $this->currentOrder = $order;

        $idOrder =  $this->_checkoutSession->getLastRealOrder()->getIncrementId();


        $this->messageManager->printMessage('success', $idOrder);
       // $this->getPriceInCents();


      //  $this->total = $total;
//        if (isset($_POST['bank_name']))
//        {
//            $this->messageManager->printMessage('success',$_POST['bank_name']);
//        }
//
       // echo $this->currentOrder->getEntityId();
           // echo $this->currentOrder->getIncrementId(); //Id current order

        //echo  $order->getCustomerEmail();
       // echo  $order->getCustomerName();


       var_dump( xdebug_info( 'mode' ) );
    }

    private function quantityMultiplyPrice($quantity, $price)
    {
        return $quantity * $price;
    }

    private function getPriceInCents()
    {
        $cents = 0;
        $quote = $this->_checkoutSession->getQuote();
        $cents = $quote->getGrandTotal() * 100;
        return $cents;
    }

    private function printDataArray()
    {
        //$this->messageManager->printMessage('success', $this->currentOrder->getIncrementId());
        try
        {
        $this->messageManager->printMessage('success', json_encode($this->helper->collectDataForRequest("EUR", $this->getPriceInCents(), "Example iDEAL payment", $this->_checkoutSession->getLastRealOrder()->getIncrementId(), "https://www.example.com/", "ideal", "INGBNL2A")));

        }
        catch (Exception $exception)
        {
            print_r($exception->getMessage());
            exit();
        }


    }

    private function printIssuers()
    {
//        $this->client = $this->myConfig->getClient($this->endpoint, $this->myConfig->getSystemInfo('api_key'));
//        $textIssuers = $this->myConfig->getIssuresJson($this->client);


        $this->messageManager->printMessage('success', $this->helper->getGeneralConfig('api_key'));

        $this->client = $this->helper->createClient($this->endpoint, $this->helper->getGeneralConfig('api_key'));
        $textIssuers = $this->helper->getIssuers($this->client);
       // $textIssuers = array("aa" => 123);
        $this->messageManager->printMessage('success', json_encode($textIssuers));
    }

//    public function assignData(\Magento\Framework\DataObject $data)
//    {
//       // $this->printDataArray();
//        $this->_eventManager->dispatch(
//            'payment_method_assign_data_' . $this->getCode(),
//            [
//                AbstractDataAssignObserver::METHOD_CODE => $this,
//                AbstractDataAssignObserver::MODEL_CODE => $this->getInfoInstance(),
//                AbstractDataAssignObserver::DATA_CODE => $data
//            ]
//        );
//
//        $this->_eventManager->dispatch(
//            'payment_method_assign_data',
//            [
//                AbstractDataAssignObserver::METHOD_CODE => $this,
//                AbstractDataAssignObserver::MODEL_CODE => $this->getInfoInstance(),
//                AbstractDataAssignObserver::DATA_CODE => $data
//            ]
//        );
//
//        return $this;
//    }
//
//
//    public function acceptPayment(InfoInterface $payment)
//    {
//        $this->printDataArray();
//        if (!$this->canReviewPayment()) {
//            throw new \Magento\Framework\Exception\LocalizedException(__('The payment review action is unavailable.'));
//        }
//        return false;
//    }
//
//    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
//    {
//        $this->printDataArray();
//        if (!$this->canAuthorize()) {
//            throw new \Magento\Framework\Exception\LocalizedException(__('The authorize action is not available.'));
//        }
//        return $this;
//    }
//
//    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
//    {
//        $this->printDataArray();
//        if (!$this->canOrder()) {
//            throw new \Magento\Framework\Exception\LocalizedException(__('The order action is not available.'));
//        }
//        return $this;
//    }

    public function getInfoInstance()
    {

        //$this->printIssuers();
        $this->printDataArray();
        $instance = $this->getData('info_instance');
        if (!$instance instanceof InfoInterface)
        {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We cannot retrieve the payment information object instance.')
            );
        }

        return $instance;
    }

//    public function initialize($paymentAction, $stateObject)
//    {
//        $this->client = $this->config->getClient($this->endpoint, $this->config->getSystemInfo('api_key'));
//        $textIssuers = $this->config->getIssuresJson($this->client);
//
//        $this->printMessage('success', $textIssuers);
//
//        $this->printMessage('success', 'This is a success message');
//        $this->printMessage('warning', 'This is a Warning message');
//        return $this;
//    }

//    public function validate()
//    {echo "This is validate";exit();}
//
//    public function  successAction()
//    {echo "This is successAction";exit();}

    /**
     * Authorize a payment.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
//    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
//    { echo 'dsfsdf';
//        exit();
//        return $this;
//    }

    /**
     * Test method to handle an API call for authorization request.
     *
     * @param $request
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
//    public function makeAuthRequest($request)
//    {
//        echo 'sddssdadssaasdda0'; nht,f ,elt cnthnb dcs ws pfrjvtyxtys
//          cnhjrb

//        exit();
//
//    }

//    public function getConfigPaymentAction()
//    {
//        echo 'dsdfsdfsdfsdsdfsfsdfsdffffffffffffffffffffffff';
//        exit();
//        return self::ACTION_AUTHORIZE_CAPTURE;
//    }

//    public function initialize($paymentAction, $stateObject)
//    {
//        echo "14475333qqqqqq";
//        exit();
//       // return parent::initialize($paymentAction, $stateObject); // TODO: Change the autogenerated stub
//    }
//
//    public function processTransaction()
//    {
//        echo "fsdfsd11111111222222222222333333333f";
//        exit();
//    }



}
