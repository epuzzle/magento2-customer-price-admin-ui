<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Provides information about the products
 */
class DataProvider extends ProductDataProvider
{
    /**
     * @var ContextInterface
     */
    private ContextInterface $context;

    /**
     * DataProvider
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param ContextInterface $context
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $modifiersPool
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        ContextInterface $context,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = [],
        PoolInterface $modifiersPool = null
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data,
            $modifiersPool
        );
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        if (!$this->collection->isLoaded()) {
            $storeId = $this->context->getFilterParam('store_id');
            $storeId = $storeId ?: $this->context->getRequestParam('store_id');
            $storeId = $storeId ?: Store::DEFAULT_STORE_ID;
            $this->collection->setStoreId((int)$storeId);

            $websiteId = $this->context->getRequestParam('website_id');
            if ($websiteId) {
                $this->collection->addWebsiteFilter((int)$websiteId);
            }
        }

        return parent::getData();
    }
}
