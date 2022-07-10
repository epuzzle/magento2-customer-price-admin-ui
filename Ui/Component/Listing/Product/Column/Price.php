<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\Product\Column;

use Magento\Catalog\Ui\Component\Listing\Columns\Price as PriceBase;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * The price column for the products
 */
class Price extends PriceBase
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * Price
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CurrencyInterface $localeCurrency
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CurrencyInterface $localeCurrency,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $localeCurrency,
            $storeManager,
            $components,
            $data
        );

        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            $storeId = $storeId ?: $this->context->getRequestParam('store_id');
            $storeId = $storeId ?: Store::DEFAULT_STORE_ID;
            $store = $this->storeManager->getStore((int)$storeId);
            $currency = $this->localeCurrency->getCurrency($store->getBaseCurrencyCode());

            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = $currency->toCurrency(sprintf("%f", $item[$fieldName]));
                }
            }
        }

        return $dataSource;
    }
}
