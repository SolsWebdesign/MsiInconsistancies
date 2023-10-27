<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Model;

use SolsWebdesign\MsiInconsistencies\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * class Config
 */
class Config implements ConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isSetflag(self::XML_PATH_ENABLED);
    }

    /**
     * @return array
     */
    public function getSendToEmailAddresses(): array
    {
        $emailAddresses = $this->getConfigValue(self::XML_PATH_SEND_EMAIL_TO) ?? '';
        return array_filter(explode(',', $emailAddresses));
    }

    /**
     * @return array
     */
    public function getSender(): array
    {
        $senderEmail = $this->getConfigValue(self::XML_PATH_SEND_FROM_EMAIL);
        $senderName = $this->getConfigValue(self::XML_PATH_SEND_FROM_NAME);

        return [
            'email' => $senderEmail,
            'name' =>$senderName
        ];
    }

    /**
     * @return array
     */
    public function getLoggingTypes(): array
    {
        return self::LOGGING_TYPES;
    }

    /**
     * @return int
     */
    public function getLoggingType(): int
    {
        $loggingType = $this->getConfigValue(self::XML_PATH_LOGGING_TYPE);
        return (int)$loggingType;
    }

    /**
     * @return int
     */
    public function getThreshold(): int
    {
        $threshold = $this->getConfigValue(self::XML_PATH_THRESHOLD);
        return (int)$threshold;
    }


    /**
     * @param $field
     * @return string|null
     */
    private function getConfigValue($field): ?string
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $field
     * @return bool
     */
    private function isSetflag($field): bool
    {
        return $this->scopeConfig->isSetFlag(
            $field,
            ScopeInterface::SCOPE_STORE
        );
    }
}