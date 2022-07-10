<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\CustomerPrice\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * The customer email column for the list of customer prices
 */
class CustomerEmail extends Column
{
    /**
     * Column name
     */
    public const NAME = 'column.customer_email';

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * CustomerEmail
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = '<a href="' . $this->urlBuilder->getUrl(
                        'customer/index/edit',
                        ['id' => $item['customer_id']]
                    ) . '" target="_blank">' . $item[$fieldName] . '</a>';
                }
            }
        }
        return $dataSource;
    }
}
