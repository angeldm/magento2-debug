<?php

namespace Angeldm\Debug\Model\View;

use Angeldm\Debug\Api\Data\ProfileInterface;
use Angeldm\Debug\Model\Profiler;
use Magento\Framework\App\Area;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Toolbar implements ArgumentInterface
{
    const COLLECTOR_PLACEHOLDER = 'debug.toolbar.collectors.%s';

    /**
     * @var \Angeldm\Debug\Api\Data\ProfileInterface
     */
    private $profile;

    /**
     * @var \Angeldm\Debug\Model\Collector\CollectorInterface[]
     */
    private $collectors;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Angeldm\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \Angeldm\Debug\Helper\Url
     */
    private $url;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Angeldm\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \Angeldm\Debug\Helper\Url $url
    ) {
        $this->layout = $layout;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->url = $url;
    }

    public function getToken(): string
    {
        return $this->getProfile() ? $this->getProfile()->getToken() : '';
    }

    public function getCollectors()
    {
        if ($this->collectors === null) {
            $this->collectors = $this->getProfile()->getCollectors();
        }

        return $this->collectors;
    }

    public function getCollectorBlocks()
    {
        $blocks = [];

        foreach ($this->getCollectors() as $name => $collector) {
            /** @var \Angeldm\Debug\Model\Collector\CollectorInterface $collector */
            if (!$block = $this->layout->getBlock(sprintf(self::COLLECTOR_PLACEHOLDER, $name))) {
                continue;
            }
            /** @var \Magento\Framework\View\Element\Template $block */
            $block->setData('collector', $collector);
            $block->setData('profiler_url', $this->url->getProfilerUrl($this->getToken(), $collector->getName()));
            $blocks[$collector->getName()] = $block;
        }

        return $blocks;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->url->getUrl($route, $params);
    }

    public function getToolbarUrl()
    {
        return $this->url->getToolbarUrl($this->getToken());
    }

    public function getAdminUrl()
    {
        return $this->backendUrl->getRouteUrl(Area::AREA_ADMINHTML);
    }

    private function getProfile(): ProfileInterface
    {
        if ($this->profile === null) {
            $this->profile = $this->profileMemoryStorage->read();
        }

        return $this->profile;
    }
}
