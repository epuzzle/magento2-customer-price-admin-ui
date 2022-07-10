<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\Customer\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Listing actions of the customers
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * Actions constructor
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
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    if ($this->context->getRequestParam('customer_id') == $item['entity_id']) {
                        continue;
                    }

                    $item[$name]['edit'] = [
                        'callback' => [
                            [
                                'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.general'
                                    . '.customer_model',
                                'target' => 'closeModal',
                            ],
                            [
                                'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.general'
                                    . '.customer_button',
                                'target' => 'updateData',
                                'params' => [
                                    'entityId' => $item['entity_id'],
                                    'options' => [
                                        [
                                            'label' => __('Email'),
                                            'value' => '<a href="mailto:' . $item['email'] . '" target="_blank">'
                                                . $item['email'] . '</a>'
                                        ],
                                        [
                                            'label' => __('Name'),
                                            'value' => '<a href="' . $this->urlBuilder->getUrl(
                                                'customer/index/edit',
                                                ['id' => $item['entity_id']]
                                            ) . '" target="_blank">' . $item['name'] . '</a>'
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
