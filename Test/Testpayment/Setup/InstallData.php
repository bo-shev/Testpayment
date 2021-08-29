<?php

namespace Test\Testpayment\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Test\Testpayment\Helper\StatusCreator;

class InstallData implements InstallDataInterface
{
    protected $statusCreator;
    protected $orderStatusInfo = array(
        '0' => array(
            'code' => 'ginger_completed',
            'state' => 'ginger_completed',
            'lable' => 'Ginger_Completed',
        ),
        '1' => array(
            'code' => 'ginger_accepted',
            'state' => 'ginger_accepted',
            'lable' => 'Ginger_Accepted',
        ),
        '2' => array(
            'code' => 'ginger_error',
            'state' => 'ginger_error',
            'lable' => 'Ginger_Error',
        ),
        '3' => array(
            'code' => 'ginger_canceled',
            'state' => 'ginger_canceled',
            'lable' => 'Ginger_Canceled',
        ),
        '4' => array(
            'code' => 'ginger_processing',
            'state' => 'ginger_processing',
            'lable' => 'Ginger_Processing',
        ),
        '5' => array(
            'code' => 'ginger_expired',
            'state' => 'ginger_expired',
            'lable' => 'Ginger_Expired',
        ),
    );

    public function __construct( \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
                                 \Magento\Sales\Model\Order\StatusFactory $statusFactory,
                                 \Magento\Sales\Model\ResourceModel\Order\StatusFactory $statusResourceFactory)
    {
        $this->statusCreator = new StatusCreator($moduleDataSetup, $statusFactory, $statusResourceFactory);
        $this->insertOrderStatuses();
    }

    private function insertOrderStatuses()
    {
        foreach ($this->orderStatusInfo as $row)
        {
            $this->statusCreator->createOrderStatus($row['code'], $row['state'], $row['lable']);
        }
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
    }
}
