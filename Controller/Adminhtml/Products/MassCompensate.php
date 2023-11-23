<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Controller\Adminhtml\Products;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect;
use SolsWebdesign\MsiInconsistencies\Api\InconsistenciesManagementInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;
use SolsWebdesign\MsiInconsistencies\Logger\Logger;

/**
 * class MassCompensate
 */
class MassCompensate extends Action
{
    /**
     * Authorization level for admin session
     */
    public const ADMIN_RESOURCE = 'SolsWebdesign_MsiInconsistencies::index';

    /**
     * @var InconsistenciesManagementInterface
     */
    private $inconsistenciesManagementInterface;

    /**
     * @var AuthSession
     */
    private $authSession;

    /**
     * @var Logger
     */
    private $logger;


    /**
     * @param Context $context
     * @param InconsistenciesManagementInterface $inconsistenciesManagementInterface
     * @param AuthSession $authSession
     * @param Logger $logger
     */
    public function __construct(
        Context                            $context,
        InconsistenciesManagementInterface $inconsistenciesManagementInterface,
        AuthSession                        $authSession,
        Logger                             $logger
    )
    {
        $this->inconsistenciesManagementInterface = $inconsistenciesManagementInterface;
        $this->authSession = $authSession;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $selectedItems = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');

        $selection = $this->authSession->getSelection();

        if (($selectedItems === null) && $excluded === 'false') {
            $selectedItems = $selection;
        }

        if (($selectedItems === null) && is_array($excluded)) {
            $selectedItems = array_diff($selection, $excluded);
        }


        $hasError = false;
        foreach ($selectedItems as $selectedItem) {
            try {
                $this->inconsistenciesManagementInterface->proceedSelectedItem($selectedItem);
            } catch (Exception $exception) {
                $this->logger->developer($exception);
                $hasError = true;
            }
        }

        if (!$hasError) {
            $this->messageManager->addSuccessMessage(__('The inconsistencies have been compensated.'));
        } else {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

}
