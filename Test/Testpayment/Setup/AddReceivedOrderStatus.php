<?php

namespace Test\Testpayment\Setup;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

class AddReceivedOrderStatus implements DataPatchInterface
{
    protected $STATUS_CODE = 'test2';
    protected $STATUS_STATE = 'test2';
    protected $STATUS_LABLE = 'test2';

    private $moduleDataSetup;

    protected $statusFactory;

    protected $statusResourceFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        $STATUS_CODE,
        $STATUS_STATE,
        $STATUS_LABLE
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->STATUS_CODE = $STATUS_CODE;
        $this->STATUS_STATE = $STATUS_STATE;
        $this->STATUS_LABLE = $STATUS_LABLE;
    }

    public function apply()
    {
        $status = $this->statusFactory->create();

        $status->setData([
            'status' => $this->STATUS_CODE,
            'label' => $this->STATUS_LABLE,
        ]);

        $statusResource = $this->statusResourceFactory->create();
        $statusResource->save($status);

        $status->assignState($this->STATUS_STATE, true, true);

        return $this;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
