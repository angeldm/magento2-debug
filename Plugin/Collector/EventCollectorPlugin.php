<?php

namespace Angeldm\Debug\Plugin\Collector;

use Angeldm\Debug\Model\ValueObject\EventObserver;
use Magento\Framework\Event\Invoker\InvokerDefault;
use Magento\Framework\Event\Observer;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EventCollectorPlugin
{
    /**
     * @var \Angeldm\Debug\Model\Collector\EventCollector
     */
    private $eventCollector;

    public function __construct(
        \Angeldm\Debug\Model\Collector\EventCollector $eventCollector
    ) {
        $this->eventCollector = $eventCollector;
    }

    public function aroundDispatch(InvokerDefault $subject, callable $proceed, array $configuration, Observer $observer)
    {
        $start = microtime(true);
        $proceed($configuration, $observer);
        $end = microtime(true);

        $this->eventCollector->log(new EventObserver(
            $configuration['name'],
            $configuration['instance'],
            $observer->getEvent()->getName(),
            $end - $start
        ));
    }
}
