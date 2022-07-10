<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Form\CustomerPrice;

use Jeysmook\CustomerPrices\Model\ResourceModel\CustomerPrice\Collection;
use Jeysmook\CustomerPrices\Model\ResourceModel\CustomerPrice\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * Data provider of the customer price entity
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * DataProvider constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $requestCollectionFactory
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $requestCollectionFactory,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $requestCollectionFactory->create();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data,
            $pool
        );
    }
}
