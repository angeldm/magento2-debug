<?php

namespace Angeldm\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class RedirectRenderer implements RendererInterface
{
    const TEMPLATE = 'Angeldm_Debug::renderer/redirect.phtml';

    /**
     * @var \Angeldm\Debug\Model\ValueObject\Redirect
     */
    private $redirect;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Angeldm\Debug\Helper\Url
     */
    private $url;

    public function __construct(
        \Angeldm\Debug\Model\ValueObject\Redirect $redirect,
        \Magento\Framework\View\LayoutInterface $layout,
        \Angeldm\Debug\Helper\Url $url
    ) {
        $this->redirect = $redirect;
        $this->layout = $layout;
        $this->url = $url;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setProfilerUrl($this->url->getProfilerUrl($this->redirect->getToken()))
            ->setRedirect($this->redirect)
            ->toHtml();
    }
}
