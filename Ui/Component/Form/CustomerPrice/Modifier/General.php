<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Form\CustomerPrice\Modifier;

use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Provide the current customer price data
 */
class General implements ModifierInterface
{
    /**
     * @var Locator
     */
    private Locator $locator;

    /**
     * General
     *
     * @param Locator $locator
     */
    public function __construct(
        Locator $locator
    ) {
        $this->locator = $locator;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data): array
    {
        $store = $this->locator->getStore();
        $itemId = $this->locator->getCustomerPrice()->getItemId();
        if ($itemId) {
            $data[$itemId] = $this->locator->getCustomerPrice()->getData();
        }

        $data[$itemId] = array_merge(
            $this->locator->getCustomerPrice()->getData(),
            [
                'currency' => $store->getBaseCurrency()->getCurrencySymbol(),
                'store_id' => $store->getId()
            ]
        );

        if ($this->locator->getRequestStoreId() || $itemId) {
            $data[$itemId]['website_id'] = $store->getWebsiteId();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
