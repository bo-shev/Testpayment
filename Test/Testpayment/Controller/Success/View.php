<?php

declare(strict_types=1);

namespace Test\Testpayment\Controller\Success;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Element\Template;
use \Test\Testpayment\Model\PaymentMethod;
use \Test\Testpayment\Helper\StatusHandler;

class View extends Action
{
    protected $_paymentMethod;
    private $order;
    private $statusHandler;

    public function __construct(Context $context,
                                \Test\Testpayment\Model\PaymentMethod $paymentMethod
                                )
    {
        parent::__construct($context);

        if (isset( $_GET["order_id"]))
        {
            $this->_paymentMethod = $paymentMethod;
            $this->order = $this->_paymentMethod->getOrderById($_GET["order_id"]);
        }

        $this->statusHandler = new StatusHandler($paymentMethod, $this->order["status"]);
        $this->statusHandler->changeStatus($this->order["merchant_order_id"], $this->statusHandler->getStatus());
    }

    public function execute()
    {
        /** @var Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        /** @var Template $block */
        if ($this->order != null)
        {
            $block = $page->getLayout()->getBlock('test.testpayment.layout.example');
            $block->setData('order_info', json_encode($this->order));
            $block->setData('order_status', $this->statusHandler->getStatus());
            $block->setData('order_description', $this->order["description"]);

            if ($this->statusHandler->getStatus() == 'error')
            {
                $block->setData('customer_message', $this->order['transactions'][0]['customer_message']);
            }
        }

        return $page;
    }
}
