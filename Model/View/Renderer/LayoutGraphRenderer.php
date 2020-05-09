<?php

namespace Angeldm\Debug\Model\View\Renderer;

use Angeldm\Debug\Model\ValueObject\Block;
use Magento\Framework\View\Element\Template;

class LayoutGraphRenderer implements RendererInterface
{
    const TEMPLATE = 'Angeldm_Debug::renderer/layout/graph.phtml';

    /**
     * @var \Angeldm\Debug\Model\ValueObject\Block[]
     */
    private $blocks;

    /**
     * @var float
     */
    private $totalRenderTime;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Angeldm\Debug\Model\ValueObject\LayoutNodeFactory
     */
    private $layoutNodeFactory;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\LayoutNodeRendererFactory
     */
    private $layoutNodeRendererFactory;

    /**
     * @var \Angeldm\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        array $blocks,
        float $totalRenderTime,
        \Magento\Framework\View\LayoutInterface $layout,
        \Angeldm\Debug\Model\ValueObject\LayoutNodeFactory $layoutNodeFactory,
        \Angeldm\Debug\Model\View\Renderer\LayoutNodeRendererFactory $layoutNodeRendererFactory,
        \Angeldm\Debug\Helper\Formatter $formatter
    ) {
        $this->blocks = $blocks;
        $this->totalRenderTime = $totalRenderTime;
        $this->layout = $layout;
        $this->layoutNodeFactory = $layoutNodeFactory;
        $this->layoutNodeRendererFactory = $layoutNodeRendererFactory;
        $this->formatter = $formatter;
    }

    public function render(): string
    {
        // Microtime formatting revert for calculations
        $this->totalRenderTime = $this->formatter->revertMicrotime($this->totalRenderTime);

        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'nodes' => $this->createNodes(),
                'layout_node_renderer' => $this->layoutNodeRendererFactory,
            ])
            ->toHtml();
    }

    private function createNodes(): array
    {
        $nodes = [];

        foreach ($this->blocks as $block) {
            if (!$block->getParentId()) {
                $children = $this->resolveChildren($block);
                $nodes[] = $this->layoutNodeFactory->create([
                    'block' => $block,
                    'layoutRenderTime' => $this->totalRenderTime,
                    'children' => $children
                ]);
            }
        }

        return $nodes;
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @param \Angeldm\Debug\Model\ValueObject\Block $block
     * @param string                                  $prefix
     * @param bool                                    $sibling
     * @return array
     */
    private function resolveChildren(Block $block, string $prefix = '', bool $sibling = false)
    {
        $children = [];
        $childrenCount = count($block->getChildren());
        $i = 1;
        $prefix .= $sibling ? '│&nbsp;' : '&nbsp;';
        foreach ($block->getChildren() as $childId) {
            $child = array_filter($this->blocks, function ($block) use ($childId) {
                /** @var \Angeldm\Debug\Model\ValueObject\Block $block */
                return $block->getName() === $childId;
            });
            if (($child = array_shift($child)) === null) {
                continue;
            }
            $childChildren = $this->resolveChildren($child, $prefix, $i++ !== $childrenCount);
            $children[$childId] = $this->layoutNodeFactory->create([
                'block' => $child,
                'layoutRenderTime' => $this->totalRenderTime,
                'prefix' => $prefix,
                'children' => $childChildren
            ]);
        }
        return $children;
    }
}
