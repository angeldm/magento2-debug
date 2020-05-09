<?php

namespace Angeldm\Debug\Model\Collector;

class PluginCollector implements CollectorInterface, LateCollectorInterface
{
    const NAME = 'plugin';

    const BEFORE = 'before';
    const AROUND = 'around';
    const AFTER  = 'after';

    /**
     * @var \Angeldm\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Angeldm\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \Angeldm\Debug\Model\Info\PluginInfo
     */
    private $pluginInfo;

    public function __construct(
        \Angeldm\Debug\Helper\Config $config,
        \Angeldm\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \Angeldm\Debug\Model\Info\PluginInfo $pluginInfo
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->pluginInfo = $pluginInfo;
    }

    public function collect(): CollectorInterface
    {
        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $this->dataCollector->setData([
            self::BEFORE => $this->pluginInfo->getBeforePlugins(),
            self::AROUND => $this->pluginInfo->getAroundPlugins(),
            self::AFTER => $this->pluginInfo->getAfterPlugins(),
        ]);

        return $this;
    }

    public function hasPlugins(): bool
    {
        return !empty($this->dataCollector->getData(self::BEFORE))
            || !empty($this->dataCollector->getData(self::AROUND))
            || !empty($this->dataCollector->getData(self::AFTER));
    }

    public function getBeforePlugins(): array
    {
        return $this->dataCollector->getData(self::BEFORE) ?? [];
    }

    public function getAroundPlugins(): array
    {
        return $this->dataCollector->getData(self::AROUND) ?? [];
    }

    public function getAfterPlugins(): array
    {
        return $this->dataCollector->getData(self::AFTER) ?? [];
    }

    public function getPluginsCount(): int
    {
        return $this->getBeforePluginsCount() + $this->getAroundPluginsCount() + $this->getBeforePluginsCount();
    }

    public function getBeforePluginsCount(): int
    {
        return array_sum(array_map('count', $this->getBeforePlugins()));
    }

    public function getAroundPluginsCount(): int
    {
        return array_sum(array_map('count', $this->getAroundPlugins()));
    }

    public function getAfterPluginsCount(): int
    {
        return array_sum(array_map('count', $this->getAfterPlugins()));
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isPluginCollectorEnabled();
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
