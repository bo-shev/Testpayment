<?php

namespace Test\Testpayment\Helper;

use \Test\Testpayment\Helper\PaymentHelper;

class Data
{
    public function __construct(Data $helper)
    {
        $object_manager     = MagentoCoreModelObjectManager::getInstance();
        $helper_factory     = $object_manager->get('MagentoCoreModelFactoryHelper');
        $this->_coreHelper  = $helper_factory->get('MagentoCoreHelperData');
        $this->helper = $helper;
    }
    public function newFunction()
    {
        $this->helper->get();
    }
}
