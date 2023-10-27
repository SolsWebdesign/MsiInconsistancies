<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Cron;

use SolsWebdesign\MsiInconsistencies\Api\EmailManagementInterface;
use SolsWebdesign\MsiInconsistencies\Logger\Logger;
use SolsWebdesign\MsiInconsistencies\Api\InconsistenciesManagementInterface;
use SolsWebdesign\MsiInconsistencies\Api\ConfigInterface;

/**
 * class InconsistenciesEmail
 */
class InconsistenciesEmail
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EmailManagementInterface
     */
    private $emailManagementInterface;

    /**
     * @var InconsistenciesManagementInterface
     */
    private $inconsistenciesManagementInterface;

    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @param Logger $logger
     * @param EmailManagementInterface $emailManagementInterface
     * @param InconsistenciesManagementInterface $inconsistenciesManagementInterface
     * @param ConfigInterface $configInterface
     */
    public function __construct(
        Logger                             $logger,
        EmailManagementInterface           $emailManagementInterface,
        InconsistenciesManagementInterface $inconsistenciesManagementInterface,
        ConfigInterface                    $configInterface
    )
    {
        $this->logger = $logger;
        $this->emailManagementInterface = $emailManagementInterface;
        $this->inconsistenciesManagementInterface = $inconsistenciesManagementInterface;
        $this->configInterface = $configInterface;
    }

    /**
     * Run the cron job
     */
    public function execute(): void
    {
        if (!$this->configInterface->isEnabled()) {
            return;
        }

        $this->logger->developer("cron:msi_inconsistencies_email_notification_cron STARTED...");
        $inconsistenciesEmailContent = $this->inconsistenciesManagementInterface->getInconsistenciesEmailContent();
        $content = $inconsistenciesEmailContent['content'];
        $threshold = $this->configInterface->getThreshold();
        $size = $inconsistenciesEmailContent['size'] ?? 0;

        if ($size && ($size === $threshold || $size > $threshold)) {
            $this->emailManagementInterface->sendEmailNotification(
                [
                    'content' => $content,
                    'size' => $size
                ]
            );
        }
    }
}
