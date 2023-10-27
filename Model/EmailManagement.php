<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Model;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use SolsWebdesign\MsiInconsistencies\Logger\Logger;
use SolsWebdesign\MsiInconsistencies\Api\EmailManagementInterface;
use SolsWebdesign\MsiInconsistencies\Api\ConfigInterface;

/**
 * class EmailManagement
 */
class EmailManagement implements EmailManagementInterface
{

    private const EMAIL_TEMPLATE = 'msi_inconsistencies_email_template';

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @param TransportBuilder $transportBuilder
     * @param Logger $logger
     * @param ConfigInterface $configInterface
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Logger $logger,
        ConfigInterface $configInterface
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->configInterface = $configInterface;
    }

    /**
     * @param $content
     * @return void
     * @throws LocalizedException
     */
    public function sendEmailNotification($content): void
    {
        $sendTo = $this->configInterface->getSendToEmailAddresses();
        $sender = $this->configInterface->getSender();

        if (!empty($sendTo)) {
            $data = [
                'template' => self::EMAIL_TEMPLATE,
                'sendTo' => $sendTo,
                'sender' => $sender,
                'content' => $content
            ];

            $this->sendEmail($data);
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws LocalizedException
     */
    private function sendEmail(array $data): void
    {
        $transportBuilder = null;

        try {
            $transportBuilder = $this->transportBuilder->setTemplateIdentifier($data['template'])
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => '0'])
                ->setTemplateVars($data['content'])
                ->setFromByScope($data['sender'])
                ->addTo($data['sendTo']);
        } catch (Exception $exception) {
            $this->logger->developer($exception);
        }

        if ($transportBuilder) {
            $transport = $transportBuilder->getTransport();

            try {
                $transport->sendMessage();
            } catch (MailException $exception) {
                $this->logger->developer($exception);
            }
        }
    }
}
