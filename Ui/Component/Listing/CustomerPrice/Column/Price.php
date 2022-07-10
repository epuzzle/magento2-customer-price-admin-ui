<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\CustomerPrice\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Zend_Currency_Exception;

/**
 * The price column for the list of customer prices
 */
class Price extends Column
{
    /**
     * Column name
     */
    public const NAME = 'column.price';

    /**
     * @var CurrencyInterface
     */
    private CurrencyInterface $localeCurrency;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var array
     */
    private array $baseCurrencyCodes = [];

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
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     * @throws Zend_Currency_Exception
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $currency = $this->localeCurrency->getCurrency(
                    $this->resolveBaseCurrencyCode((int)$item['website_id'])
                );
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = $currency->toCurrency(sprintf("%f", $item[$fieldName]));
                }
            }
        }

        return $dataSource;
    }

    /**
     * Resolve base currency code
     *
     * @param int $websiteId
     * @return string
     * @throws NoSuchEntityException
     */
    private function resolveBaseCurrencyCode(int $websiteId): string
    {
        if (!isset($this->baseCurrencyCodes[$websiteId])) {
            foreach ($this->storeManager->getStores() as $store) {
                if ($store->getWebsiteId() == $websiteId) {
                    $this->baseCurrencyCodes[$websiteId] = $store->getBaseCurrencyCode();
                }
            }
        }

        if (!isset($this->baseCurrencyCodes[$websiteId])) {
            $defaultStore = $this->storeManager->getStore(
                $this->context->getFilterParam('store_id', Store::DEFAULT_STORE_ID)
            );
            $this->baseCurrencyCodes[$websiteId] = $defaultStore->getBaseCurrencyCode();
        }

        return $this->baseCurrencyCodes[$websiteId];
    }
}
