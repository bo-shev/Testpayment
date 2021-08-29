<?php
namespace Test\Testpayment\Model;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use mysql_xdevapi\Exception;
use Test\Testpayment\Helper\MessageManager;
use \Magento\Payment\Model\Method\AbstractMethod;
use Test\Testpayment\Helper\StatusCreator;

use Test\Testpayment\Setup\AddReceivedOrderStatus;

use Magento\Framework\Setup\ModuleDataSetupInterface;

class PaymentMethod extends AbstractMethod
{

    const CODE = 'testpayment';

    protected $_code = self::CODE;

    protected $_canAuthorize = true;
    protected $_isGateway = true;
    protected $_isOffline = false;
    protected $_isInitializeNeeded = true;

    protected $_checkoutSession;
    protected $_product;
    protected $objectManager;
    protected $total;
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $orderResource;
    protected $orderFactory;

    private $orderInfo;
    private $currentOrder;
    private $helper;
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
        \Test\Testpayment\Helper\PaymentHelper $helperData,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order $orderInfo,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderInterface $order
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

        $this->orderInfo = $orderInfo;
        $this->helper = $helperData;
        $this->messageManager = new MessageManager($messageManager);
        $this->client = $this->helper->createClient($this->endpoint, $this->helper->getGeneralConfig('api_key'));
        $this->orderRepository = $orderRepository;
        $this->_checkoutSession = $checkoutSession;
        $this->currentOrder = $order;
    }


    private function redirectAfterCreatingOrder($url)
    {
        header('Location: '.$url);
    }

    public function afterPaymentAction($order)
    {
        $this->redirectAfterCreatingOrder($order["transactions"]["0"]["payment_url"]);
    }

    public function getOrderById($id)
    {
        $tempClient = $this->helper->createClient($this->endpoint, $this->helper->getGeneralConfig('api_key'));
        return $tempClient->getOrder($id);
    }

    public function getPriceInCents()
    {
        $cents = 0;
        $cents = $this->_checkoutSession->getLastRealOrder()->getGrandTotal() * 100;
        return $cents;
    }

    public function getMagentoOrderById($orderId)
    {
        return $this->orderInfo->loadByIncrementId($orderId);
    }

    public function changeOrderStatus($order, $status)
    {
        $order->setStatus($status);
        $order->save();
    }

    public function getCurrentOrderId()
    {
        return $this->_checkoutSession->getLastRealOrder()->getIncrementId();
    }

    public function getDataArrayForRequest()
    {
        return $this->helper->collectDataForRequest("EUR", $this->getPriceInCents(), "Your order number is: ".$this->getCurrentOrderId(), $this->getCurrentOrderId(), "https://magento.test/testpayment/success/view", "ideal", $this->getIssuerId(0));
    }

    public function createOrder()
    {
        return $this->helper->createNewOrder($this->client, $this->getDataArrayForRequest());
    }

    public function getIssuerId($idIssuer)
    {
        $issuer = $this->helper->getIssuers($this->client);
        return $issuer[$idIssuer]['id'];
    }
}
