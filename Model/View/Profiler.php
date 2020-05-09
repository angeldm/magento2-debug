<?php

namespace Angeldm\Debug\Model\View;

use Angeldm\Debug\Api\Data\ProfileInterface;
use Angeldm\Debug\Helper\Formatter;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zend\Stdlib\ParametersInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Profiler implements ArgumentInterface
{
    /**
     * @var \Angeldm\Debug\Model\View\Renderer\TraceRendererFactory
     */
    private $traceRendererFactory;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\LayoutGraphRendererFactory
     */
    private $layoutGraphRendererFactory;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\ParametersRendererFactory
     */
    private $parametersRendererFactory;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\QueryParametersRendererFactory
     */
    private $queryParametersRendererFactory;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\QueryRendererFactory
     */
    private $queryRendererFactory;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\QueryListFactory
     */
    private $queryListRendererFactory;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\TableRendererFactory
     */
    private $tableRendererFactory;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\VarRendererFactory
     */
    private $varRendererFactory;

    /**
     * @var \Angeldm\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \Angeldm\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \Angeldm\Debug\Model\View\Renderer\TraceRendererFactory $traceRendererFactory,
        \Angeldm\Debug\Model\View\Renderer\LayoutGraphRendererFactory $layoutGraphRendererFactory,
        \Angeldm\Debug\Model\View\Renderer\ParametersRendererFactory $parametersRendererFactory,
        \Angeldm\Debug\Model\View\Renderer\QueryParametersRendererFactory $queryParametersRendererFactory,
        \Angeldm\Debug\Model\View\Renderer\QueryRendererFactory $queryRendererFactory,
        \Angeldm\Debug\Model\View\Renderer\QueryListRendererFactory $queryListRendererFactory,
        \Angeldm\Debug\Model\View\Renderer\TableRendererFactory $tableRendererFactory,
        \Angeldm\Debug\Model\View\Renderer\VarRendererFactory $varRendererFactory,
        \Angeldm\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \Angeldm\Debug\Helper\Formatter $formatter
    ) {
        $this->traceRendererFactory = $traceRendererFactory;
        $this->layoutGraphRendererFactory = $layoutGraphRendererFactory;
        $this->parametersRendererFactory = $parametersRendererFactory;
        $this->queryParametersRendererFactory = $queryParametersRendererFactory;
        $this->queryRendererFactory = $queryRendererFactory;
        $this->queryListRendererFactory = $queryListRendererFactory;
        $this->tableRendererFactory = $tableRendererFactory;
        $this->varRendererFactory = $varRendererFactory;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->formatter = $formatter;
    }

    public function renderLayoutGraph(array $blocks, float $totalTime): string
    {
        return $this->layoutGraphRendererFactory->create([
            'blocks' => $blocks,
            'totalRenderTime' => $totalTime
        ])->render();
    }

    public function renderTrace(array $trace): string
    {
        return $this->traceRendererFactory->create(['trace' => $trace])->render();
    }

    public function renderParameters(ParametersInterface $parameters): string
    {
        return $this->parametersRendererFactory->create(['parameters' => $parameters])->render();
    }

    public function renderQueryParameters(string $query, array $parameters): string
    {
        return $this->queryParametersRendererFactory->create([
            'query' => $query,
            'parameters' => $parameters
        ])->render();
    }

    public function renderQuery(string $query): string
    {
        return $this->queryRendererFactory->create(['query' => $query])->render();
    }

    public function renderQueryList(array $queries): string
    {
        return $this->queryListRendererFactory->create(['queries' => $queries])->render();
    }

    public function renderTable(array $items, array $labels = []): string
    {
        return $this->tableRendererFactory->create(['items' => $items, 'labels' => $labels])->render();
    }

    public function dump($variable): string
    {
        return $this->varRendererFactory->create(['variable' => $variable])->render();
    }

    public function getProfile(): ProfileInterface
    {
        return $this->profileMemoryStorage->read();
    }

    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }
}
