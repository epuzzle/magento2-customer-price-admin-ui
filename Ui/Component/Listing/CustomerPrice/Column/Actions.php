<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\CustomerPrice\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Listing actions of the customer prices
 */
class Actions extends Column
{
    private const PATH_EDIT = 'epuzzle/customerPrice/edit';
    private const PATH_DELETE = 'epuzzle/customerPrice/delete';
    private const PATH_TO_PRODUCT = 'catalog/product/edit';
    private const PATH_TO_CUSTOMER = 'customer/index/edit';

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * Actions
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
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['item_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::PATH_EDIT,
                            ['item_id' => $item['item_id']]
                        ),
                        'label' => __('Edit'),
                        '__disableTmpl' => true,
                    ];

                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::PATH_DELETE,
                            ['item_id' => $item['item_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $item['item_id']),
                            'message' => __('Are you sure you want to delete a %1 record?', $item['item_id']),
                            '__disableTmpl' => true,
                        ],
                        'post' => true,
                        '__disableTmpl' => true,
                    ];

                    $item[$name]['to_product'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::PATH_TO_PRODUCT,
                            ['id' => $item['product_id']]
                        ),
                        'label' => __('Go to Product'),
                        '__disableTmpl' => true,
                    ];

                    $item[$name]['to_customer'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::PATH_TO_CUSTOMER,
                            ['id' => $item['customer_id']]
                        ),
                        'label' => __('Go to Customer'),
                        '__disableTmpl' => true,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
