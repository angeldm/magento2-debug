<?php

namespace Angeldm\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class LayoutNodeRenderer implements RendererInterface
{
    const TEMPLATE = 'Angeldm_Debug::renderer/layout/node.phtml';

    /**
     * @var \Angeldm\Debug\Model\ValueObject\LayoutNode
     */
    private $node;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\LayoutNodeRendererFactory
     */
    private $layoutNodeRendererFactory;

    /**
     * @var \Angeldm\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \Angeldm\Debug\Model\ValueObject\LayoutNode $node,
        \Magento\Framework\View\LayoutInterface $layout,
        \Angeldm\Debug\Model\View\Renderer\LayoutNodeRendererFactory $layoutNodeRendererFactory,
        \Angeldm\Debug\Helper\Formatter $formatter
    ) {
        $this->node = $node;
        $this->layout = $layout;
        $this->layoutNodeRendererFactory = $layoutNodeRendererFactory;
        $this->formatter = $formatter;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'node' => $this->node,
                'formatter' => $this->formatter,
                'layout_node_renderer' => $this->layoutNodeRendererFactory,
            ])
            ->toHtml();
    }
}
