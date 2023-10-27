<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Logger;

use Monolog\Logger as MonologLogger;
use SolsWebdesign\MsiInconsistencies\Api\ConfigInterface;

/**
 * class Logger
 */
class Logger extends MonologLogger
{

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @param ConfigInterface $config
     * @param $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        ConfigInterface $config,
                        $name,
        array           $handlers = [],
        array           $processors = []
    )
    {
        $this->config = $config;
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * @param $message
     * @param array $context
     * @return void
     */
    public function regular($message, array $context = []): void
    {
        if ($this->config->getLoggingType() > ConfigInterface::LOGGING_TYPE_NONE) {
            $this->addRecord(static::INFO, (string)$message, $context);
        }
    }

    /**
     * @param $message
     * @param array $context
     * @return void
     */
    public function developer($message, array $context = []): void
    {
        if ($this->config->getLoggingType() === ConfigInterface::LOGGING_TYPE_DEVELOPMENT) {
            $this->addRecord(static::DEBUG, (string)$message, $context);
        }
    }
}
