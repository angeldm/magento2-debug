<?php

namespace Angeldm\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class QueryListRenderer implements RendererInterface
{
    const TEMPLATE = 'Angeldm_Debug::renderer/query/list.phtml';

    /**
     * @var \Zend_Db_Profiler_Query[]
     */
    private $queries;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\QueryRendererFactory
     */
    private $queryRendererFactory;

    /**
     * @var \Angeldm\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        array $queries,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Math\Random $mathRandom,
        \Angeldm\Debug\Model\View\Renderer\QueryRendererFactory $queryRendererFactory,
        \Angeldm\Debug\Helper\Formatter $formatter
    ) {
        $this->queries = $queries;
        $this->layout = $layout;
        $this->mathRandom = $mathRandom;
        $this->queryRendererFactory = $queryRendererFactory;
        $this->formatter = $formatter;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'queries' => $this->queries,
                'query_renderer' => $this->queryRendererFactory,
                'prefix' => $this->mathRandom->getUniqueHash(),
                'formatter' => $this->formatter,
            ])
            ->toHtml();
    }
}
