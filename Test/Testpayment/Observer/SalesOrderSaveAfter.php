<?php
namespace Test\Testpayment\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use \Test\Testpayment\Model\PaymentMethod;
use Vendor;

class SalesOrderSaveAfter implements ObserverInterface
{
    /** @var \Magento\Framework\Logger\Monolog */

    protected $logger;
    protected $_paymentMethod;

    public function __construct(\Psr\Log\LoggerInterface $loggerInterface,
                                \Test\Testpayment\Model\PaymentMethod $paymentMethod)
    {
        $this->logger = $loggerInterface;
        $this->_paymentMethod = $paymentMethod;
    }

    public function execute(Observer $observer)
    {
        $this->_paymentMethod->afterPaymentAction($this->_paymentMethod->createOrder());
    }
}
