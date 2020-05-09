<?php

namespace Angeldm\Debug\Observer;

use Angeldm\Debug\Model\Info\RequestInfo;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ValidateRedirect implements ObserverInterface
{
    /**
     * @var \Angeldm\Debug\Model\Session
     */
    private $session;

    public function __construct(
        \Angeldm\Debug\Model\Session\Proxy $session
    ) {
        $this->session = $session;
    }

    public function execute(Observer $observer)
    {
        if ($this->session->getData(RequestInfo::REDIRECT_PARAM)) {
            $observer->getRequest()->setParam('_redirected', true);
        }
    }
}
