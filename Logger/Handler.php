<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Logger;

use Magento\Framework\Logger\Handler\Base;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * class Handler
 */
class Handler extends Base
{
    /**
     * Handler constructor.
     * @param DriverInterface $filesystem
     * @param null $filePath
     * @param null $fileName
     * @throws \Exception
     */
    public function __construct(
        DriverInterface $filesystem,
        $filePath = null,
        $fileName = null
    ) {
        $this->filesystem = $filesystem;
        $this->fileName = '/var/log/Msi_Inconsistencies_' . date("m") . '.log';

        parent::__construct($filesystem, $filePath, $fileName);
    }
}
