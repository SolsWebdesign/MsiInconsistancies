<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Api;

use Magento\InventoryReservationsApi\Model\ReservationInterface;

/**
 * Class InconsistenciesManagementInterface
 *
 * @api
 */
interface InconsistenciesManagementInterface
{
    /**
     * @return array
     */
    public function getInconsistenciesList(): array;

    /**
     * @return array
     */
    public function getInconsistenciesEmailContent(): array;

    /**
     * @param array $arguments
     * @return ReservationInterface
     */
    public function createReservationModel(array $arguments): ReservationInterface;

    /**
     * @param $selectedItem
     * @return void
     */
    public function proceedSelectedItem($selectedItem): void;
}