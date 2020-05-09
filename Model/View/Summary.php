<?php

namespace Angeldm\Debug\Model\View;

use Angeldm\Debug\Api\Data\ProfileInterface;
use Angeldm\Debug\Model\ValueObject\Redirect;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Summary implements ArgumentInterface
{
    /**
     * @var \Angeldm\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \Angeldm\Debug\Helper\Url
     */
    private $url;

    /**
     * @var \Angeldm\Debug\Model\View\Renderer\RedirectRendererFactory
     */
    private $redirectRendererFactory;

    public function __construct(
        \Angeldm\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \Angeldm\Debug\Helper\Url $url,
        \Angeldm\Debug\Model\View\Renderer\RedirectRendererFactory $redirectRendererFactory
    ) {
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->url = $url;
        $this->redirectRendererFactory = $redirectRendererFactory;
    }

    public function getProfile(): ProfileInterface
    {
        return $this->profileMemoryStorage->read();
    }

    public function getProfilerUrl($token): string
    {
        return $this->url->getProfilerUrl($token);
    }

    public function renderRedirect(Redirect $redirect): string
    {
        return $this->redirectRendererFactory->create(['redirect' => $redirect])->render();
    }
}
