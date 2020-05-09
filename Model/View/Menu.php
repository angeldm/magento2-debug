<?php

namespace Angeldm\Debug\Model\View;

use Angeldm\Debug\Api\Data\ProfileInterface;
use Angeldm\Debug\Model\Collector\CollectorInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Menu implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Angeldm\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \Angeldm\Debug\Helper\Url
     */
    private $url;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Angeldm\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \Angeldm\Debug\Helper\Url $url
    ) {
        $this->request = $request;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->url = $url;
    }

    public function isActive(string $collectorName): bool
    {
        return $this->getProfile()->hasCollector($collectorName);
    }

    public function isCurrentPanel(string $collectorName): bool
    {
        return $this->request->getParam('panel') === $collectorName;
    }

    public function getCollector(string $collectorName): CollectorInterface
    {
        return $this->getProfile()->getCollector($collectorName);
    }

    public function getProfilerUrl(string $collectorName): string
    {
        return $this->url->getProfilerUrl($this->getProfile()->getToken(), $collectorName);
    }

    public function getConfigurationUrl(): string
    {
        return $this->url->getConfigurationUrl();
    }

    private function getProfile(): ProfileInterface
    {
        return $this->profileMemoryStorage->read();
    }
}
