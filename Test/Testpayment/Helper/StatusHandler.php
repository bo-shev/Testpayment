<?php

namespace Test\Testpayment\Helper;

class StatusHandler
{
    protected $status;
    protected $paymentMethod;
    protected $statusParser = array(
        'completed' => 'ginger_completed',
        'accepted' => 'ginger_accepted',
        'error' => 'ginger_error',
        'processing' => 'ginger_processing',
        'canceled' => 'ginger_canceled',
        'expired' => 'ginger_expired',
    );

    public function __construct(\Test\Testpayment\Model\PaymentMethod $paymentMethod, $status)
    {
        $this->status = $status;
        $this->paymentMethod = $paymentMethod;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function changeStatus($orderId, $status)
    {
        $this->paymentMethod->changeOrderStatus($this->paymentMethod->getMagentoOrderById($orderId), $this->statusParser[$status]);
    }

}
