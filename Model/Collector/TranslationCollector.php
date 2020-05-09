<?php

namespace Angeldm\Debug\Model\Collector;

use Angeldm\Debug\Logger\LoggableInterface;

class TranslationCollector implements CollectorInterface, LoggerCollectorInterface
{
    const NAME = 'translation';

    const TRANSLATIONS = 'translations';
    const DEFINED = 'defined';
    const MISSING = 'missing';

    /**
     * @var \Angeldm\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Angeldm\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \Angeldm\Debug\Logger\DataLogger
     */
    private $dataLogger;

    public function __construct(
        \Angeldm\Debug\Helper\Config $config,
        \Angeldm\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \Angeldm\Debug\Logger\DataLoggerFactory $dataLoggerFactory
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->dataLogger = $dataLoggerFactory->create();
    }

    public function collect(): CollectorInterface
    {
        $defined = [];
        $missing = [];
        /** @var \Angeldm\Debug\Model\ValueObject\Translation $translation */
        foreach ($this->dataLogger->getLogs() as $translation) {
            if ($translation->isDefined()) {
                $defined[$translation->getId()] = $translation->getTranslation();
                continue;
            }
            $missing[$translation->getId()] = $translation->getPhrase();
        }

        $this->dataCollector->setData([
            self::TRANSLATIONS => [
                self::DEFINED => $defined,
                self::MISSING => $missing,
            ],
        ]);

        return $this;
    }

    public function getTranslations(): array
    {
        return $this->dataCollector->getData(self::TRANSLATIONS) ?? [];
    }

    public function getDefinedTranslations(): array
    {
        return $this->dataCollector->getData(self::TRANSLATIONS)[self::DEFINED] ?? [];
    }

    public function getMissingTranslations(): array
    {
        return $this->dataCollector->getData(self::TRANSLATIONS)[self::MISSING] ?? [];
    }

    public function getTotal(): int
    {
        return count($this->getDefinedTranslations()) + count($this->getMissingTranslations());
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isTranslationCollectorEnabled();
    }

    public function getData(): array
    {
        return $this->dataCollector->getData();
    }

    public function setData(array $data): CollectorInterface
    {
        $this->dataCollector->setData($data);

        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        if (!empty($this->getMissingTranslations())) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_DEFAULT;
    }

    public function log(LoggableInterface $value): LoggerCollectorInterface
    {
        $this->dataLogger->log($value);

        return $this;
    }
}
