<?php

namespace Angeldm\Debug\Controller\Profiler;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;

class Purge extends Action
{
    /**
     * @var \Angeldm\Debug\Model\Storage\ProfileFileStorage
     */
    private $profileFileStorage;

    /**
     * @var \Angeldm\Debug\Logger\Logger
     */
    private $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Angeldm\Debug\Model\Storage\ProfileFileStorage $profileFileStorage,
        \Angeldm\Debug\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->profileFileStorage = $profileFileStorage;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $this->profileFileStorage->purge();
        } catch (FileSystemException $e) {
            $this->logger->critical($e);
        }

        /** @var  $resultRedirect */
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
            ->setUrl($this->_redirect->getRefererUrl());
    }
}
