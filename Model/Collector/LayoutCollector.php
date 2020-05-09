<?php

namespace Angeldm\Debug\Model\Collector;

use Angeldm\Debug\Logger\LoggableInterface;

class LayoutCollector implements CollectorInterface, LoggerCollectorInterface
{
    const NAME = 'layout';

    const BLOCK_PROFILER_ID_KEY        = 'debug_profiler_id';
    const BLOCK_START_RENDER_KEY       = 'debug_start_render';
    const BLOCK_STOP_RENDER_KEY        = 'debug_stop_render';
    const BLOCK_RENDER_TIME_KEY        = 'debug_render_time';
    const BLOCK_HASH_KEY               = 'debug_hash';
    const BLOCK_PARENT_PROFILER_ID_KEY = 'debug_profiler_parent_id';

    const HANDLES             = 'handles';
    const BLOCKS              = 'blocks';
    const BLOCKS_CREATED      = 'blocks_created';
    const BLOCKS_RENDERED     = 'blocks_rendered';
    const BLOCKS_NOT_RENDERED = 'blocks_not_rendered';
    const TOTAL_RENDER_TIME   = 'total_render_time';
    const RENDER_TIME         = 'render_time';

    /**
     * @var \Angeldm\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Angeldm\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \Angeldm\Debug\Logger\DataLogger
     */
    private $dataLogger;

    /**
     * @var \Angeldm\Debug\Model\Info\LayoutInfo
     */
    private $layoutInfo;

    /**
     * @var \Angeldm\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \Angeldm\Debug\Helper\Config $config,
        \Angeldm\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \Angeldm\Debug\Logger\DataLoggerFactory $dataLogger,
        \Angeldm\Debug\Model\Info\LayoutInfo $layoutInfo,
        \Angeldm\Debug\Helper\Formatter $formatter
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->dataLogger = $dataLogger->create();
        $this->layoutInfo = $layoutInfo;
        $this->formatter = $formatter;
    }

    public function collect(): CollectorInterface
    {
        $renderTime = 0;

        /** @var \Angeldm\Debug\Model\ValueObject\Block $block */
        foreach ($this->dataLogger->getLogs() as $block) {
            $renderTime += $block->getRenderTime();
        }

        $this->dataCollector->setData([
            self::TOTAL_RENDER_TIME   => $renderTime,
            self::HANDLES             => $this->layoutInfo->getHandles(),
            self::BLOCKS_CREATED      => $this->layoutInfo->getCreatedBlocks(),
            self::BLOCKS_RENDERED     => $this->dataLogger->getLogs(),
            self::BLOCKS_NOT_RENDERED => $this->layoutInfo->getNotRenderedBlocks(),
        ]);

        return $this;
    }

    public function getRenderTime(): string
    {
        return $this->formatter->microtime($this->dataCollector->getData(self::TOTAL_RENDER_TIME));
    }

    public function getHandles(): array
    {
        return $this->dataCollector->getData(self::HANDLES) ?? [];
    }

    public function getCreatedBlocks(): array
    {
        return $this->dataCollector->getData(self::BLOCKS_CREATED) ?? [];
    }

    public function getRenderedBlocks(): array
    {
        return $this->dataCollector->getData(self::BLOCKS_RENDERED) ?? [];
    }

    public function getNotRenderedBlocks(): array
    {
        return $this->dataCollector->getData(self::BLOCKS_NOT_RENDERED) ?? [];
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isLayoutCollectorEnabled();
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
        if (!empty($this->getNotRenderedBlocks())) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_DEFAULT;
    }

    public function log(LoggableInterface $value): LoggerCollectorInterface
    {
        $this->dataLogger->log($value);

        return $this;
    }
}
