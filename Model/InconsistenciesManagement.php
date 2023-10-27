<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Model;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Validation\ValidationException;
use SolsWebdesign\MsiInconsistencies\Api\InconsistenciesManagementInterface;
use Magento\InventoryReservationCli\Model\GetSalableQuantityInconsistencies;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryReservationsApi\Model\ReservationBuilderInterface;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use SolsWebdesign\MsiInconsistencies\Logger\Logger;
use Magento\Backend\Model\Auth\Session as AuthSession;

/**
 * class InconsistenciesManagement
 */
class InconsistenciesManagement implements InconsistenciesManagementInterface
{

    /**
     * @var GetSalableQuantityInconsistencies
     */
    private $salableQuantityInconsistencies;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ReservationBuilderInterface
     */
    private $reservationBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var AppendReservationsInterface
     */
    private $appendReservations;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var AuthSession
     */
    private $authSession;

    /**
     * @param GetSalableQuantityInconsistencies $salableQuantityInconsistencies
     * @param SerializerInterface $serializer
     * @param OrderRepositoryInterface $orderRepository
     * @param ReservationBuilderInterface $reservationBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AppendReservationsInterface $appendReservations
     * @param Logger $logger
     * @param AuthSession $authSession
     */
    public function __construct(
        GetSalableQuantityInconsistencies $salableQuantityInconsistencies,
        SerializerInterface               $serializer,
        OrderRepositoryInterface          $orderRepository,
        ReservationBuilderInterface       $reservationBuilder,
        SearchCriteriaBuilder             $searchCriteriaBuilder,
        AppendReservationsInterface       $appendReservations,
        Logger                            $logger,
        AuthSession $authSession
    )
    {
        $this->salableQuantityInconsistencies = $salableQuantityInconsistencies;
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
        $this->reservationBuilder = $reservationBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->appendReservations = $appendReservations;
        $this->logger = $logger;
        $this->authSession = $authSession;
    }

    /**
     * @return array
     */
    public function getInconsistenciesList(): array
    {
        $result = [];
        $selection = [];

        foreach ($this->salableQuantityInconsistencies->execute(50) as $inconsistencies) {
            foreach ($inconsistencies as $inconsistency) {
                $compensationArguments = $this->compensationArguments($inconsistency);
                $result[$inconsistency->getOrderIncrementId()] = ['order_id' => $inconsistency->getOrderIncrementId(), 'items' =>
                    $this->prettifyItems($inconsistency), 'compensation_arguments' => $compensationArguments];
                $selection[$inconsistency->getOrderIncrementId()] = $compensationArguments;
            }
        }
        $this->authSession->setSelection($selection);
        return $result;
    }

    /**
     * @return array
     */
    public function getInconsistenciesEmailContent(): array
    {
        $content = '';
        $inconsistencies = $this->getInconsistenciesList();
        foreach ($inconsistencies as $key => $value) {
            $content .= '<tr>';
            $content .= '<td>' . $key . '</td>';
            $content .= '<td>' . $value['items'] . '</td>';
            $content .= '</tr>';
        }

        return [
            'content' => $content,
            'size' => count($inconsistencies)
        ];
    }

    /**
     * @param array $arguments
     * @return ReservationInterface
     * @throws ValidationException
     */
    public function createReservationModel(array $arguments): ReservationInterface
    {
        $results = $this->orderRepository->getList(
            $this->searchCriteriaBuilder->addFilter('increment_id', $arguments['order_id'], 'eq')->create()
        );
        $order = current($results->getItems());

        return $this->reservationBuilder
            ->setSku((string)$arguments['sku'])
            ->setQuantity((float)$arguments['qty'])
            ->setStockId((int)$arguments['stock_id'])
            ->setMetadata(
                $this->serializer->serialize(
                    [
                        'event_type' => 'manual_compensation',
                        'object_type' => 'order',
                        'object_id' => $order->getEntityId(),
                        'object_increment_id' => $order->getIncrementId(),
                    ]
                )
            )
            ->build();
    }

    /**
     * @param $selectedItem
     * @return void
     * @throws ValidationException
     * @throws CouldNotSaveException
     * @throws InputException
     */
    public function proceedSelectedItem($selectedItem): void
    {
        $adminUser = $this->authSession->getName();
        $items = $this->serializer->unserialize($selectedItem);
        foreach ($items as $item) {
                $compensation = $this->createReservationModel($item);
                $this->appendReservations->execute([$compensation]);
                $this->logger->regular(sprintf('Msi Inconsistency for order %s, SKU: %s has been compensated from %s',
                    $item['order_id'],
                    $item['sku'], $adminUser));
        }
    }

    /**
     * @param $inconsistency
     * @return string
     */
    private function prettifyItems($inconsistency): string
    {
        $result = '';
        $inconsistentItems = $inconsistency->getItems();
        foreach ($inconsistentItems as $sku => $qty) {
            $result .= sprintf(
                'Product <strong>%s</strong> should be compensated by '
                . '<strong>%+f</strong> for stock <strong>%s</strong>%s',
                $sku,
                -$qty,
                $inconsistency->getStockId(),
                PHP_EOL
            );
        }
        return nl2br($result);
    }

    /**
     * @param $inconsistency
     * @return string
     */
    private function compensationArguments($inconsistency): string
    {
        $result = [];
        $inconsistentItems = $inconsistency->getItems();
        foreach ($inconsistentItems as $sku => $qty) {
            $result[] = [
                'order_id' => $inconsistency->getOrderIncrementId(),
                'sku' => $sku,
                'qty' => -$qty,
                'stock_id' => $inconsistency->getStockId()
            ];

        }
        return $this->serializer->serialize($result);
    }
}
