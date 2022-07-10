<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Block\Adminhtml\CustomerPrice\Edit;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Provides common functionality for the buttons
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GenericButton
{
    /**
     * @var Context
     */
    private Context $context;

    /**
     * @var CustomerPriceRepositoryInterface
     */
    private CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * GenericButton
     *
     * @param Context $context
     * @param CustomerPriceRepositoryInterface $customerPriceRepository
     */
    public function __construct(
        Context $context,
        CustomerPriceRepositoryInterface $customerPriceRepository
    ) {
        $this->context = $context;
        $this->customerPriceRepository = $customerPriceRepository;
    }

    /**
     * Get customer price ID
     *
     * @return int|null
     */
    public function getItemId(): ?int
    {
        try {
            return (int)$this->customerPriceRepository->get(
                (int)$this->context->getRequest()->getParam('item_id')
            )->getItemId();
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
