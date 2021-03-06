<?php

namespace Transom\Tema\Helper;

class ShopTransomFunctions extends \Magento\Framework\App\Helper\AbstractHelper {
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}