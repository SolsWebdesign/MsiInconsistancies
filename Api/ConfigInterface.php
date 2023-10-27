<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Api;

/**
 * Class ConfigInterface
 *
 * @api
 */
interface ConfigInterface
{
    public const XML_PATH_ENABLED = 'sols_webdesign_msi_inconsistencies/general/enabled';
    public const XML_PATH_SEND_EMAIL_TO = 'sols_webdesign_msi_inconsistencies/general/send_email_to';
    public const XML_PATH_SEND_FROM_EMAIL = 'trans_email/ident_sales/email';
    public const XML_PATH_SEND_FROM_NAME = 'trans_email/ident_sales/name';
    public const XML_PATH_THRESHOLD = 'sols_webdesign_msi_inconsistencies/general/threshold';
    public const XML_PATH_LOGGING_TYPE = 'sols_webdesign_msi_inconsistencies/general/loggingtype';
    public const LOGGING_TYPE_NONE = 0;
    public const LOGGING_TYPE_REGULAR = 1;
    public const LOGGING_TYPE_DEVELOPMENT = 2;
    public const LOGGING_TYPES = [
        self::LOGGING_TYPE_NONE => 'None',
        self::LOGGING_TYPE_REGULAR => 'Regular',
        self::LOGGING_TYPE_DEVELOPMENT => 'Development',
    ];

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @return array
     */
    public function getSendToEmailAddresses(): array;

    /**
     * @return array
     */
    public function getSender(): array;

    /**
     * @return int
     */
    public function getThreshold(): int;

    /**
     * @return array
     */
    public function getLoggingTypes(): array;

    /**
     * @return int
     */
    public function getLoggingType(): int;

}