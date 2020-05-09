<?php

namespace Angeldm\Debug\Model\Indexer;

use Angeldm\Debug\Api\Data\ProfileInterface;
use Magento\Framework\Exception\FileSystemException;

class ProfileIndexer
{
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileSystem;

    /**
     * @var \Magento\Framework\Filesystem\File\WriteFactory
     */
    private $fileWriteFactory;

    /**
     * @var \Angeldm\Debug\Logger\Logger
     */
    private $logger;

    /**
     * @var \Angeldm\Debug\Helper\File
     */
    private $fileHelper;

    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileSystem,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Angeldm\Debug\Logger\Logger $logger,
        \Angeldm\Debug\Helper\File $fileHelper
    ) {
        $this->fileSystem = $fileSystem;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->logger = $logger;
        $this->fileHelper = $fileHelper;
    }

    public function index(ProfileInterface $profile): ProfileIndexer
    {
        try {
            $tmpIndexPath = $this->fileHelper->getProfileTempIndex();
            $this->fileSystem->createDirectory($this->fileSystem->getParentDirectory($tmpIndexPath));
            $tmpIndex = $this->fileWriteFactory->create($tmpIndexPath, $this->fileSystem, 'w');

            $tmpIndex->writeCsv($profile->getIndex());
            $index = $tmpIndex->readAll();
            $tmpIndex->close();

            try {
                $index .= $this->fileSystem->fileGetContents($this->fileHelper->getProfileIndex());
            } catch (FileSystemException $e) {
                $this->logger->info($e);
            }

            $this->fileSystem->filePutContents($this->fileHelper->getProfileIndex(), $index);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $this;
    }
}
