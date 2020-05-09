<?php

namespace Angeldm\Debug\Model\Storage;

use Angeldm\Debug\Api\Data\ProfileInterface;
use Angeldm\Debug\Model\Profile\Criteria;
use Angeldm\Debug\Model\ValueObject\SearchResult;
use Magento\Framework\Exception\FileSystemException;

class ProfileFileStorage
{
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileSystem;

    /**
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    private $fileReadFactory;

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

    /**
     * @var \Angeldm\Debug\Model\ProfileFactory
     */
    private $profileFactory;

    /**
     * @var \Angeldm\Debug\Model\Serializer\ProfileSerializer
     */
    private $profileSerializer;

    /**
     * @var \Angeldm\Debug\Model\Indexer\ProfileIndexer
     */
    private $profileIndexer;

    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileSystem,
        \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Angeldm\Debug\Logger\Logger $logger,
        \Angeldm\Debug\Helper\File $fileHelper,
        \Angeldm\Debug\Model\ProfileFactory $profileFactory,
        \Angeldm\Debug\Model\Serializer\ProfileSerializer $profileSerializer,
        \Angeldm\Debug\Model\Indexer\ProfileIndexer $profileIndexer
    ) {
        $this->fileSystem = $fileSystem;
        $this->fileReadFactory = $fileReadFactory;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->logger = $logger;
        $this->fileHelper = $fileHelper;
        $this->profileFactory = $profileFactory;
        $this->profileSerializer = $profileSerializer;
        $this->profileIndexer = $profileIndexer;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param \Angeldm\Debug\Model\Profile\Criteria $criteria
     * @return array
     */
    public function find(Criteria $criteria): array
    {
        $results = [];

        try {
            if (!$this->fileSystem->isExists($this->fileHelper->getProfileIndex())) {
                return $results;
            }

            $resource = $this->fileSystem->fileOpen($this->fileHelper->getProfileIndex(), 'r');
            $i = 0;
            while ($profile = $this->fileSystem->fileGetCsv($resource)) {
                if ($criteria->match($profile)) {
                    $results[] = SearchResult::createFromCsv($profile);
                    if (++$i >= $criteria->getLimit()) {
                        break;
                    }
                }
            }

            $this->fileSystem->fileClose($resource);
        } catch (FileSystemException $e) {
            $this->logger->critical($e);
        }

        return $results;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function purge()
    {
        $this->fileSystem->deleteDirectory($this->fileHelper->getProfileDirectory());
    }

    /**
     * @param $token
     * @return \Angeldm\Debug\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function read($token): ProfileInterface
    {
        $file = $this->fileReadFactory->create($this->fileHelper->getProfileFilename($token), $this->fileSystem);

        return $this->profileSerializer->unserialize($file->readAll());
    }

    /**
     * @param \Angeldm\Debug\Api\Data\ProfileInterface $profile
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function write(ProfileInterface $profile)
    {
        $path = $this->fileHelper->getProfileFilename($profile->getToken());
        $this->fileSystem->createDirectory($this->fileSystem->getParentDirectory($path));
        $file = $this->fileWriteFactory->create($path, $this->fileSystem, 'w');
        $file->write($this->profileSerializer->serialize($profile));
        $file->close();
        $profile->setFilesize($this->fileSystem->stat($path)['size']);

        $this->profileIndexer->index($profile);

        return $path;
    }

    /**
     * @param string $token
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function remove(string $token)
    {
        $path = $this->fileHelper->getProfileFilename($token);
        $this->fileSystem->deleteFile($path);
    }
}
