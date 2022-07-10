<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Model\Customer;

use EPuzzle\CustomerPrice\Model\ResourceModel\CustomerPrice;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Get the customer by customer price ID
 */
class GetCustomerByCustomerPriceId
{
    /**
     * @var CustomerPrice
     */
    private CustomerPrice $resource;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * GetCustomerByCustomerPriceId
     *
     * @param CustomerPrice $resource
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerPrice $resource,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->resource = $resource;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get the product by customer price ID
     *
     * @param int $customerPriceId
     * @return CustomerInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(int $customerPriceId): CustomerInterface
    {
        $select = $this->resource->getConnection()->select();
        $select->from($this->resource->getMainTable(), ['customer_id']);
        $select->where('item_id = ?', $customerPriceId);
        $select->limit(1);
        $customerId = $this->resource->getConnection()->fetchOne($select);
        if (!$customerId) {
            throw new NoSuchEntityException();
        }

        return $this->customerRepository->getById((int)$customerId);
    }
}
