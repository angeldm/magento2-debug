<?php

namespace Angeldm\Debug\Helper;

use Angeldm\Debug\Exception\CollectorNotFoundException;
use Angeldm\Debug\Model\Config\Source\ErrorHandler;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Config
{
    const CONFIG_ENABLED               = 'Angeldm_Debug/general/active';
    const CONFIG_ENABLED_ADMINHTML     = 'Angeldm_Debug/general/active_adminhtml';
    const CONFIG_ALLOWED_IPS           = 'Angeldm_Debug/general/allowed_ips';
    const CONFIG_ERROR_HANDLER         = 'Angeldm_Debug/general/error_handler';
    const CONFIG_TIME_PRECISION        = 'Angeldm_Debug/time/precision';
    const CONFIG_PERFORMANCE_COLOR     = 'Angeldm_Debug/performance/%s_color';
    const CONFIG_COLLECTOR_AJAX        = 'Angeldm_Debug/collector/ajax';
    const CONFIG_COLLECTOR_CACHE       = 'Angeldm_Debug/collector/cache';
    const CONFIG_COLLECTOR_CONFIG      = 'Angeldm_Debug/collector/config';
    const CONFIG_COLLECTOR_CUSTOMER    = 'Angeldm_Debug/collector/customer';
    const CONFIG_COLLECTOR_DATABASE    = 'Angeldm_Debug/collector/database';
    const CONFIG_COLLECTOR_EVENT       = 'Angeldm_Debug/collector/event';
    const CONFIG_COLLECTOR_PLUGIN      = 'Angeldm_Debug/collector/plugin';
    const CONFIG_COLLECTOR_LAYOUT      = 'Angeldm_Debug/collector/layout';
    const CONFIG_COLLECTOR_MEMORY      = 'Angeldm_Debug/collector/memory';
    const CONFIG_COLLECTOR_MODEL       = 'Angeldm_Debug/collector/model';
    const CONFIG_COLLECTOR_TIME        = 'Angeldm_Debug/collector/time';
    const CONFIG_COLLECTOR_TRANSLATION = 'Angeldm_Debug/collector/translation';

    const COLLECTORS = 'Angeldm_Debug/profiler/collectors';

    /**
     * @var \Magento\Framework\PhraseFactory
     */
    private $phraseFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var \Angeldm\Debug\Model\Storage\HttpStorage
     */
    private $httpStorage;

    public function __construct(
        \Magento\Framework\PhraseFactory $phraseFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Angeldm\Debug\Model\Storage\HttpStorage $httpStorage
    ) {
        $this->phraseFactory = $phraseFactory;
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
        $this->deploymentConfig = $deploymentConfig;
        $this->httpStorage = $httpStorage;
    }

    public function getErrorHandler(): string
    {
        if (!$this->isEnabled()) {
            return ErrorHandler::MAGENTO;
        }

        return $this->scopeConfig->getValue(self::CONFIG_ERROR_HANDLER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isEnabled(): bool
    {
        if ($this->appState->getMode() !== \Magento\Framework\App\State::MODE_DEVELOPER) {
            return false;
        }

        if (!$this->isActive()) {
            return false;
        }

        try {
            if ($this->appState->getAreaCode() === Area::AREA_ADMINHTML && !$this->isAdminhtmlActive()) {
                return false;
            }
        } catch (LocalizedException $e) {
            return true;
        }

        return true;
    }

    public function isActive(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isAdminhtmlActive(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ENABLED_ADMINHTML, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isFrontend(): bool
    {
        return $this->appState->getAreaCode() === Area::AREA_FRONTEND;
    }

    public function getAllowedIPs(): array
    {
        return array_filter(array_map('trim', explode(',', $this->scopeConfig->getValue(
            self::CONFIG_ALLOWED_IPS,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ))));
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return bool
     */
    public function isAllowedIP(): bool
    {
        if (empty($this->getAllowedIPs())) {
            return true;
        }

        return in_array($_SERVER['REMOTE_ADDR'], $this->getAllowedIPs());
    }

    public function getTimePrecision(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_TIME_PRECISION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function getCollectors(): array
    {
        return $this->scopeConfig->getValue(self::COLLECTORS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @param string $name
     * @return string
     * @throws \Angeldm\Debug\Exception\CollectorNotFoundException
     */
    public function getCollectorClass(string $name): string
    {
        if (!isset($this->getCollectors()[$name])) {
            throw new CollectorNotFoundException($this->phraseFactory->create([
                'text' => 'Collector "%1" not found',
                'arguments' => $name
            ]));
        }

        return $this->getCollectors()[$name];
    }

    public function isAjaxCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_AJAX,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isCacheCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_CACHE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isConfigCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_CONFIG,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isCustomerCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_CUSTOMER,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isDatabaseCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_DATABASE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && $this->deploymentConfig->get('db/connection/default/profiler/enabled');
    }

    public function isEventCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_EVENT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isPluginCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_PLUGIN,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isLayoutCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_LAYOUT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && !$this->httpStorage->isFPCRequest();
    }

    public function isMemoryCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_MEMORY,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isModelCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_MODEL,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isTimeCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_TIME,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isTranslationCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_TRANSLATION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && !$this->httpStorage->isFPCRequest();
    }

    public function getPerformanceColor(string $event): string
    {
        return $this->scopeConfig->getValue(
            sprintf(self::CONFIG_PERFORMANCE_COLOR, $event),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}
