<?php

namespace Test\Testpayment\Helper;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Test\Testpayment\Setup\AddReceivedOrderStatus;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

class StatusCreator
{
    private $moduleDataSetup;
    private $statusFactory;
    private $statusResourceFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory)
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }

    public function createOrderStatus($status_code, $status_state, $status_lable)
    {
        $newStatus = new AddReceivedOrderStatus($this->moduleDataSetup, $this->statusFactory, $this->statusResourceFactory, $status_code, $status_state, $status_lable);
        $newStatus->apply();
    }

}
