<?php

namespace Angeldm\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class QueryRenderer implements RendererInterface
{
    const TEMPLATE = 'Angeldm_Debug::renderer/query.phtml';

    /**
     * @var \Zend_Db_Profiler_Query
     */
    private $query;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\VarRendererFactory
     */
    private $varRendererFactory;

    /**
     * @var \Angeldm\Debug\Helper\Database
     */
    private $databaseHelper;

    public function __construct(
        \Zend_Db_Profiler_Query $query,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Math\Random $mathRandom,
        \Angeldm\Debug\Model\View\Renderer\VarRendererFactory $varRendererFactory,
        \Angeldm\Debug\Helper\Database $databaseHelper
    ) {
        $this->query = $query;
        $this->layout = $layout;
        $this->mathRandom = $mathRandom;
        $this->varRendererFactory = $varRendererFactory;
        $this->databaseHelper = $databaseHelper;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'query' => $this->query,
                'highlighted_query' => $this->databaseHelper->highlightQuery($this->query->getQuery()),
                'formatted_query' => $this->databaseHelper->formatQuery($this->query->getQuery()),
                'runnable_query' => $this->databaseHelper->highlightQuery(
                    $this->databaseHelper->replaceQueryParameters(
                        $this->query->getQuery(),
                        $this->query->getQueryParams()
                    )
                ),
                'var_renderer' => $this->varRendererFactory,
                'uniq_id' => $this->mathRandom->getUniqueHash(),
            ])
            ->toHtml();
    }
}
