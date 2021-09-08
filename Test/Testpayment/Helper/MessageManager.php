<?php

namespace Test\Testpayment\Helper;

class MessageManager
{
    private $_messageManager;

    public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->_messageManager = $messageManager;
    }

    public function printMessage($type, $text)
    {
        switch ($type)
        {
            case 'success':  $this->_messageManager->addSuccess(__($text)); break;
            case 'warning':  $this->_messageManager->addWarning(__($text)); break;
            case 'notice':  $this->_messageManager->addNotice(__($text)); break;
            default:  $this->_messageManager->addError("Wrong type! Use something like 'success' or 'notice'"); break;
        }
    }
}
