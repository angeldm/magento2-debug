<?php

namespace Angeldm\Debug\Observer;

use Angeldm\Debug\Model\Profiler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class DebugHandle implements ObserverInterface
{
    /**
     * @var \Angeldm\Debug\Helper\Config
     */
    private $config;

    public function __construct(
        \Angeldm\Debug\Helper\Config $config
    ) {
        $this->config = $config;
    }

    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $observer->getLayout()->getUpdate()->addHandle('Angeldm_Debug');
        }

        if ($observer->getFullActionName() === Profiler::TOOLBAR_FULL_ACTION_NAME) {
            $observer->getLayout()->getUpdate()->removeHandle('default');
        }
    }
}
