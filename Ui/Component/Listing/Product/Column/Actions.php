<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\Product\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Listing actions of the products
 */
class Actions extends Column
{
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
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    if ($this->context->getRequestParam('product_id') == $item['entity_id']) {
                        continue;
                    }

                    $item[$name]['edit'] = [
                        'callback' => [
                            [
                                'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.general'
                                    . '.product_model',
                                'target' => 'closeModal',
                            ],
                            [
                                'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.general'
                                    . '.product_button',
                                'target' => 'updateData',
                                'params' => [
                                    'entityId' => $item['entity_id'],
                                    'options' => [
                                        [
                                            'label' => __('SKU'),
                                            'value' => $item['sku']
                                        ],
                                        [
                                            'label' => __('Name'),
                                            'value' => '<a href="' . $this->urlBuilder->getUrl(
                                                'catalog/product/edit',
                                                ['id' => $item['entity_id'], 'store' => $item['store_id']]
                                            ) . '" target="_blank">' . $item['name'] . '</a>'
                                        ],
                                        [
                                            'label' => __('Price'),
                                            'value' => $item['price'] ?? 'N/A'
                                        ]
                                    ]
                                ],
                            ],
                        ],
                        'href' => '#',
                        'label' => __('Assign'),
                        '__disableTmpl' => true,
                    ];
                }
            }
        }
        return $dataSource;
    }
}
