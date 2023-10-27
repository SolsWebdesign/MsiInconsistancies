<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Controller\Adminhtml\Products;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\MediaContentApi\Model\Config;
use Magento\Backend\App\Action\Context;


/**
 * class Index
 */
class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'SolsWebdesign_MsiInconsistencies::index';

    /**
     * @var Config
     */
    private $config;

    /**
     * Index constructor.
     * @param Context $context
     * @param Config $config
     */


    /**
     * Get the media gallery layout
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Msi Inconsistencies'));

        return $resultPage;
    }
}