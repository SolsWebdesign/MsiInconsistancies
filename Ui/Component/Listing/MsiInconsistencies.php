<?php
/**
 * Product : Sols Webdesign Msi Inconsistencies
 *
 * @copyright Copyright © 2023 Sols Webdesign. All rights reserved.
 * @author    Peter Banchev
 */

namespace SolsWebdesign\MsiInconsistencies\Ui\Component\Listing;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use SolsWebdesign\MsiInconsistencies\Api\InconsistenciesManagementInterface;

/**
 * class UncategorizedProducts
 */
class MsiInconsistencies extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $addFieldStrategies;

    /**
     * @var array
     */
    protected $addFilterStrategies;

    /**
     * @var PoolInterface
     */
    private $modifiersPool;

    /**
     * @var InconsistenciesManagementInterface
     */
    private $inconsistenciesManagementInterface;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param InconsistenciesManagementInterface $inconsistenciesManagementInterface
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $modifiersPool
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        InconsistenciesManagementInterface
        $inconsistenciesManagementInterface,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = [],
        PoolInterface $modifiersPool = null
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->modifiersPool = $modifiersPool ?: ObjectManager::getInstance()->get(PoolInterface::class);
        $this->collection->setStoreId(Store::DEFAULT_STORE_ID);
        $this->inconsistenciesManagementInterface = $inconsistenciesManagementInterface;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getData(): array
    {
        $inconsistenciesList = $this->inconsistenciesManagementInterface->getInconsistenciesList();

        $data = [
            'totalRecords' => count($inconsistenciesList),
            'items' => array_values($inconsistenciesList),
        ];

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }
        return $data;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
