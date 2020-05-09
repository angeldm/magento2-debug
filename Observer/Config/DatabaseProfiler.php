<?php

namespace Angeldm\Debug\Observer\Config;

use Angeldm\Debug\Helper\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;

class DatabaseProfiler implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\Manager
     */
    private $messageManager;

    /**
     * @var \Angeldm\Debug\Model\Config\Database\ProfilerWriter
     */
    private $dbProfilerWriter;

    /**
     * @var \Angeldm\Debug\Helper\Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Angeldm\Debug\Model\Config\Database\ProfilerWriter $dbProfilerWriter,
        \Angeldm\Debug\Helper\Config $config
    ) {
        $this->messageManager = $messageManager;
        $this->dbProfilerWriter = $dbProfilerWriter;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->isDBProfilerDependentConfigChanged($observer->getChangedPaths())) {
            return;
        }

        $flag = $this->config->isDatabaseCollectorEnabled() && $this->config->isActive();

        try {
            $this->dbProfilerWriter->save($flag);
        } catch (FileSystemException $e) {
            $this->messageManager->addExceptionMessage($e);
        }
    }

    private function isDBProfilerDependentConfigChanged(array $paths): bool
    {
        return in_array(Config::CONFIG_COLLECTOR_DATABASE, $paths) || in_array(Config::CONFIG_ENABLED, $paths);
    }
}
