<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Model\Product;

use EPuzzle\CustomerPrice\Model\ResourceModel\CustomerPrice;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Get the product by customer price ID
 */
class GetProductByCustomerPriceId
{
    /**
     * @var CustomerPrice
     */
    private CustomerPrice $resource;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * GetProductByCustomerPriceId
     *
     * @param CustomerPrice $resource
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CustomerPrice $resource,
        ProductRepositoryInterface $productRepository
    ) {
        $this->resource = $resource;
        $this->productRepository = $productRepository;
    }

    /**
     * Get the product by customer price ID
     *
     * @param int $customerPriceId
     * @param int|null $storeId
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $customerPriceId, int $storeId = null): ProductInterface
    {
        $select = $this->resource->getConnection()->select();
        $select->from($this->resource->getMainTable(), ['product_id']);
        $select->where('item_id = ?', $customerPriceId);
        $select->limit(1);
        $productId = $this->resource->getConnection()->fetchOne($select);
        if (!$productId) {
            throw new NoSuchEntityException(__('Could not get the product.'));
        }

        return $this->productRepository->getById((int)$productId, false, $storeId);
    }
}
