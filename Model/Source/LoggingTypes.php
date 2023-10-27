<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use SolsWebdesign\MsiInconsistencies\Api\ConfigInterface;

class LoggingTypes implements OptionSourceInterface
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    )
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $loggingTypesArr = $this->config->getLoggingTypes();
        $loggingTypes = [];

        foreach ($loggingTypesArr as $key => $logType) {
            $loggingTypes[] = ['label' => $logType, 'value' => $key];
        }
        return $loggingTypes;
    }
}