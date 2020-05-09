<?php

namespace Angeldm\Debug\Model\Collector;

class MemoryCollector implements CollectorInterface, LateCollectorInterface
{
    const NAME = 'memory';

    const MEMORY_USAGE = 'memory_usage';
    const MEMORY_LIMIT = 'memory_limit';

    /**
     * @var \Angeldm\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Angeldm\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \Angeldm\Debug\Model\Info\MemoryInfo
     */
    private $memoryInfo;

    /**
     * @var \Angeldm\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \Angeldm\Debug\Helper\Config $config,
        \Angeldm\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \Angeldm\Debug\Model\Info\MemoryInfo $memoryInfo,
        \Angeldm\Debug\Helper\Formatter $formatter
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->memoryInfo = $memoryInfo;
        $this->formatter = $formatter;
    }

    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            self::MEMORY_USAGE => $this->memoryInfo->getCurrentPeakMemoryUsage(),
            self::MEMORY_LIMIT => $this->memoryInfo->getCurrentMemoryLimit(),
        ]);

        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $this->dataCollector->addData(self::MEMORY_USAGE, $this->memoryInfo->getCurrentPeakMemoryUsage());

        return $this;
    }

    public function getMemoryUsage()
    {
        return $this->formatter->toMegaBytes($this->dataCollector->getData(self::MEMORY_USAGE), 1);
    }

    public function getMemoryLimit()
    {
        return $this->formatter->toMegaBytes($this->dataCollector->getData(self::MEMORY_LIMIT));
    }

    public function hasMemoryLimit()
    {
        return $this->dataCollector->getData(self::MEMORY_LIMIT) !== -1;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isMemoryCollectorEnabled();
    }

    public function getData(): array
    {
        return $this->dataCollector->getData();
    }

    public function setData(array $data): CollectorInterface
    {
        $this->dataCollector->setData($data);

        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        return self::STATUS_DEFAULT;
    }
}
