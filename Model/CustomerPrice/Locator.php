<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use EPuzzle\CustomerPrice\Api\Data\CustomerPriceInterface;
use EPuzzle\CustomerPrice\Api\Data\CustomerPriceInterfaceFactory;
use EPuzzle\CustomerPriceAdminUi\Model\Customer\GetCustomerByCustomerPriceId;
use EPuzzle\CustomerPriceAdminUi\Model\Product\GetProductByCustomerPriceId;
use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Getting information about the customer price for adminhtml area
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Locator
{
    /**
     * @var CustomerPriceRepositoryInterface
     */
    private CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * @var CustomerPriceInterfaceFactory
     */
    private CustomerPriceInterfaceFactory $customerPriceFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var GetProductByCustomerPriceId
     */
    private GetProductByCustomerPriceId $getProductByCustomerPriceId;

    /**
     * @var GetCustomerByCustomerPriceId
     */
    private GetCustomerByCustomerPriceId $getCustomerByCustomerPriceId;

    /**
     * @var ProductInterfaceFactory
     */
    private ProductInterfaceFactory $productFactory;

    /**
     * @var CustomerInterfaceFactory
     */
    private CustomerInterfaceFactory $customerFactory;

    /**
     * @var array
     */
    private array $cache = [];

    /**
     * Locator
     *
     * @param CustomerPriceRepositoryInterface $customerPriceRepository
     * @param CustomerPriceInterfaceFactory $customerPriceFactory
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param GetProductByCustomerPriceId $getProductByCustomerPriceId
     * @param GetCustomerByCustomerPriceId $getCustomerByCustomerPriceId
     * @param ProductInterfaceFactory $productFactory
     * @param CustomerInterfaceFactory $customerFactory
     */
    public function __construct(
        CustomerPriceRepositoryInterface $customerPriceRepository,
        CustomerPriceInterfaceFactory $customerPriceFactory,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        GetProductByCustomerPriceId $getProductByCustomerPriceId,
        GetCustomerByCustomerPriceId $getCustomerByCustomerPriceId,
        ProductInterfaceFactory $productFactory,
        CustomerInterfaceFactory $customerFactory
    ) {
        $this->customerPriceRepository = $customerPriceRepository;
        $this->customerPriceFactory = $customerPriceFactory;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->getProductByCustomerPriceId = $getProductByCustomerPriceId;
        $this->getCustomerByCustomerPriceId = $getCustomerByCustomerPriceId;
        $this->productFactory = $productFactory;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Get the customer price
     *
     * @return CustomerPriceInterface
     */
    public function getCustomerPrice(): CustomerPriceInterface
    {
        $cacheKey = 'customer_price';
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        try {
            $customerPrice = $this->customerPriceRepository->get(
                (int)$this->request->getParam('item_id')
            );
        } catch (Exception $exception) {
            $customerPrice = $this->customerPriceFactory->create();
        }

        return $this->cache[$cacheKey] = $customerPrice;
    }

    /**
     * Get selected store
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore(): StoreInterface
    {
        $storeId = $this->request->getParam('store');
        $storeId = $storeId ?: ((($store = $this->resolveStore()) ? $store->getId() : null) ?: null);
        $cacheKey = $storeId ? 'store_' . $storeId : 'store_default';
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        try {
            return $this->cache[$cacheKey] = $storeId
                ? $this->storeManager->getStore($storeId)
                : $this->storeManager->getStore();
        } catch (Exception $exception) {
            throw new NoSuchEntityException(__('The store not found.'));
        }
    }

    /**
     * Get selected website
     *
     * @return WebsiteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getWebsite(): WebsiteInterface
    {
        return $this->storeManager->getWebsite((int)$this->getStore()->getWebsiteId());
    }

    /**
     * Get the related product of the customer price
     *
     * @return ProductInterface
     */
    public function getProduct(): ProductInterface
    {
        if (isset($this->cache['product'])) {
            return $this->cache['product'];
        }

        try {
            return $this->cache['product'] = $this->getProductByCustomerPriceId->execute(
                (int)$this->getCustomerPrice()->getItemId(),
                (int)$this->getStore()->getId()
            );
        } catch (NoSuchEntityException $exception) {
            return $this->cache['product'] = $this->productFactory->create();
        }
    }

    /**
     * Get the related customer of the customer price
     *
     * @return CustomerInterface
     */
    public function getCustomer(): CustomerInterface
    {
        if (isset($this->cache['customer'])) {
            return $this->cache['customer'];
        }

        try {
            return $this->cache['customer'] = $this->getCustomerByCustomerPriceId->execute(
                (int)$this->getCustomerPrice()->getItemId()
            );
        } catch (LocalizedException $exception) {
            return $this->cache['customer'] = $this->customerFactory->create();
        }
    }

    /**
     * Get store ID from the request
     *
     * @return int|null
     */
    public function getRequestStoreId(): ?int
    {
        $storeId = $this->request->getParam('store');
        return (string)$storeId !== '' ? (int)$storeId : null;
    }

    /**
     * Resolves the store for the customer price
     *
     * @return StoreInterface|null
     */
    private function resolveStore(): ?StoreInterface
    {
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getWebsiteId() == $this->getCustomerPrice()->getWebsiteId()) {
                return $store;
            }
        }
        return null;
    }
}
