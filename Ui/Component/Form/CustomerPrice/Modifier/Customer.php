<?php

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Form\CustomerPrice\Modifier;

use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use Exception;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Modal;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Provides the UI part of the customer chooser
 */
class Customer implements ModifierInterface
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var Locator
     */
    private Locator $locator;

    /**
     * @var CurrencyInterface
     */
    private CurrencyInterface $localeCurrency;

    /**
     * Customer
     *
     * @param UrlInterface $urlBuilder
     * @param Locator $locator
     * @param CurrencyInterface $localeCurrency
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Locator $locator,
        CurrencyInterface $localeCurrency
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->locator = $locator;
        $this->localeCurrency = $localeCurrency;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function modifyData(array $data): array
    {
        $itemId = $this->locator->getCustomerPrice()->getItemId();
        $customer = $this->locator->getCustomer();
        if (isset($data[$itemId]) && !empty($customer->getId())) {
            $data[$itemId]['customer_id'] = $customer->getId();
            $data[$itemId]['customer_options'] = [
                [
                    'label' => __('Email'),
                    'value' => '<a href="mailto:' . $customer->getEmail() . '" target="_blank">'
                        . $customer->getEmail() . '</a>'
                ],
                [
                    'label' => __('Name'),
                    'value' => '<a href="' . $this->urlBuilder->getUrl(
                        'customer/index/edit',
                        ['id' => $customer->getId()]
                    ) . '" target="_blank">' . $customer->getFirstname() . ' ' . $customer->getLastname() . '</a>'
                ]
            ];
        }
        return $data;
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function modifyMeta(array $meta): array
    {
        $scopePrefix = 'epuzzle_customer_price_form.epuzzle_customer_price_form';
        $listing = 'epuzzle_customer_prices_customer_listing';
        $buttonSet = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'container',
                        'component' => 'EPuzzle_CustomerPriceAdminUi/js/form/components/chooser-button',
                        'title' => __('Customer'),
                        'require' => true,
                        'requireMessage' => __('The customer is required.'),
                        'externalProvider' => $listing . '.' . $listing . '_data_source',
                        'scopeLabel' => '[global]',
                        'links' => [
                            'entityId' => '${ $.provider }:data.customer_id',
                            'options' => '${ $.provider }:data.customer_options',
                            '__disableTmpl' => ['options' => false, 'entityId' => false]
                        ],
                        'sortOrder' => 20
                    ],
                ],
            ],
            'children' => [
                'button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'component',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'displayArea' => 'button',
                                'actions' => [
                                    [
                                        'targetName' => $scopePrefix . '.general.customer_model',
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $scopePrefix . '.general.customer_model.' . $listing,
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => __('Select customer'),
                                'provider' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => __('Select customer'),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ]
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listing => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listing,
                                'externalProvider' => $listing . '.' . $listing . '_data_source',
                                'selectionsProvider' => $listing . '.' . $listing . '.customer_columns.ids',
                                'ns' => $listing,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => ['imports' => false, 'exports' => true],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'customerId' => '${ $.provider }:data.customer_id',
                                    'websiteId' => '${ $.provider }:data.website_id',
                                    '__disableTmpl' => ['customerId' => false, 'websiteId' => false]
                                ],
                                'exports' => [
                                    'customerId' => '${ $.externalProvider }:params.customer_id',
                                    'websiteId' => '${ $.externalProvider }:params.website_id',
                                    '__disableTmpl' => ['customerId' => false, 'websiteId' => false]
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return array_merge_recursive(
            $meta,
            [
                'general' => [
                    'children' => [
                        'customer_button' => $buttonSet,
                        'customer_model' => $modal
                    ]
                ]
            ]
        );
    }
}
