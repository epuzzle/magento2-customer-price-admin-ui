<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Ui\Component\Form\CustomerPrice\Modifier;

use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Modal;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Provides the UI part of the product chooser
 */
class Product implements ModifierInterface
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
     * Product
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
        $storeId = $this->locator->getStore()->getId();
        $product = $this->locator->getProduct();
        if (isset($data[$itemId]) && !empty($product->getId())) {
            $data[$itemId]['product_id'] = $product->getId();
            $data[$itemId]['product_options'] = [
                [
                    'label' => __('SKU'),
                    'value' => $product->getSku()
                ],
                [
                    'label' => __('Name'),
                    'value' => '<a href="' . $this->urlBuilder->getUrl(
                        'catalog/product/edit',
                        ['id' => $product->getId(), 'store' => $storeId]
                    ) . '" target="_blank">' . $product->getName() . '</a>'
                ],
                [
                    'label' => __('Price'),
                    'value' => $this->getFormatedPrice($product)
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
        $listing = 'epuzzle_customer_prices_product_listing';
        $buttonSet = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'container',
                        'component' => 'EPuzzle_CustomerPriceAdminUi/js/form/components/chooser-button',
                        'title' => __('Product'),
                        'require' => true,
                        'requireMessage' => __('The product is required.'),
                        'externalProvider' => $listing . '.' . $listing . '_data_source',
                        'scopeLabel' => '[global]',
                        'links' => [
                            'entityId' => '${ $.provider }:data.product_id',
                            'options' => '${ $.provider }:data.product_options',
                            '__disableTmpl' => ['options' => false, 'entityId' => false]
                        ],
                        'sortOrder' => 10
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
                                        'targetName' => $scopePrefix . '.general.product_model',
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $scopePrefix . '.general.product_model.' . $listing,
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => __('Select product'),
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
                            'title' => __('Select product'),
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
                                'selectionsProvider' => $listing . '.' . $listing . '.product_columns.ids',
                                'ns' => $listing,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => ['imports' => false, 'exports' => true],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product_id',
                                    'storeId' => '${ $.provider }:data.store_id',
                                    'websiteId' => '${ $.provider }:data.website_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false, 'websiteId' => false]
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.product_id',
                                    'storeId' => '${ $.externalProvider }:params.store_id',
                                    'websiteId' => '${ $.externalProvider }:params.website_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false, 'websiteId' => false]
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
                        'product_button' => $buttonSet,
                        'product_model' => $modal
                    ]
                ]
            ]
        );
    }

    /**
     * Get formatted product price
     *
     * @param ProductInterface $product
     * @return string
     * @throws Exception
     */
    private function getFormatedPrice(ProductInterface $product): string
    {
        $currency = $this->localeCurrency->getCurrency(
            $this->locator->getStore()->getBaseCurrencyCode()
        );
        return $currency->toCurrency(sprintf("%f", $product->getPrice()));
    }
}
