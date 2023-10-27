<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Api;

/**
 * Class EmailManagementInterface
 *
 * @api
 */
interface EmailManagementInterface
{
    /**
     * @param $content
     * @return void
     */
    public function sendEmailNotification($content): void;

}