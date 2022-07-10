<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Model\ResourceModel\CustomerPrice\Grid;

use EPuzzle\CustomerPrice\Model\ResourceModel\CustomerPrice;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Psr\Log\LoggerInterface;

/**
 * Grid collection of the customer price entities
 *
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Collection extends CustomerPrice\Collection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private AggregationInterface $aggregations;

    /**
     * Collection
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $model
     * @param string $resourceModel
     * @param string $mainTable
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        string $eventPrefix = 'epuzzle_customer_price_collection_grid',
        string $eventObject = 'customer_price_collection_grid',
        string $model = Document::class,
        string $resourceModel = CustomerPrice::class,
        string $mainTable = CustomerPrice::TABLE_NAME
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_map['fields']['product_sku'] = 'product.sku';
        $this->_map['fields']['customer_email'] = 'customer.email';
        $this->_map['fields']['website_id'] = 'main_table.website_id';
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinInner(
            ['product' => $this->getTable('catalog_product_entity')],
            'product.entity_id = main_table.product_id',
            ['product_sku' => 'product.sku']
        );
        $this->getSelect()->joinInner(
            ['customer' => $this->getTable('customer_entity')],
            'customer.entity_id = main_table.customer_id',
            ['customer_email' => 'customer.email']
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @inheritDoc
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @inheritDoc
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
