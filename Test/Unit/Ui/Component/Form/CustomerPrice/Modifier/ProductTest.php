<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Form\CustomerPrice\Modifier;

use EPuzzle\CustomerPrice\Model\CustomerPrice;
use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use EPuzzle\CustomerPriceAdminUi\Ui\Component\Form\CustomerPrice\Modifier\Product as ProductModifier;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Framework\Currency;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see ProductModifier
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ProductTest extends TestCase
{
    /**
     * @var UrlInterface|MockObject
     */
    private UrlInterface $urlBuilder;

    /**
     * @var CustomerPrice|MockObject
     */
    private CustomerPrice $customerPrice;

    /**
     * @var Product|MockObject
     */
    private Product $product;

    /**
     * @var Store|MockObject
     */
    private store $store;

    /**
     * @var Locator|MockObject
     */
    private Locator $locator;

    /**
     * @var Currency|MockObject
     */
    private Currency $currency;

    /**
     * @var ProductModifier
     */
    private ProductModifier $productModifier;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->customerPrice = $this->createMock(CustomerPrice::class);
        $this->product = $this->createMock(Product::class);
        $this->store = $this->createMock(Store::class);
        $this->store->expects($this->any())
            ->method('getBaseCurrencyCode')
            ->willReturn('USD');
        $this->locator = $this->createMock(Locator::class);
        $this->locator->expects($this->any())
            ->method('getCustomerPrice')
            ->willReturn($this->customerPrice);
        $this->locator->expects($this->any())
            ->method('getProduct')
            ->willReturn($this->product);
        $this->locator->expects($this->any())
            ->method('getStore')
            ->willReturn($this->store);
        $this->currency = $this->createMock(Currency::class);
        $localeCurrency = $this->createMock(CurrencyInterface::class);
        $localeCurrency->expects($this->any())
            ->method('getCurrency')
            ->with('USD')
            ->willReturn($this->currency);
        $this->productModifier = new ProductModifier(
            $this->urlBuilder,
            $this->locator,
            $localeCurrency
        );
    }

    /**
     * @see ProductModifier::modifyData()
     */
    public function testModifyData(): void
    {
        $expectedValue = [
            1 => [
                'product_id' => 1,
                'product_options' => [
                    [
                        'label' => __('SKU'),
                        'value' => 'sku',
                    ],
                    [
                        'label' => __('Name'),
                        'value' => '<a href="https://example.com/admin/customerPrice"'
                            . ' target="_blank">Name</a>'
                    ],
                    [
                        'label' => __('Price'),
                        'value' => '$10.00'
                    ]
                ]
            ]
        ];

        $itemId = 1;
        $productId = 1;
        $storeId = 1;
        $productUrl = 'https://example.com/admin/customerPrice';
        $this->customerPrice->expects($this->once())
            ->method('getItemId')
            ->willReturn($itemId);
        $this->product->expects($this->any())
            ->method('getId')
            ->willReturn($productId);
        $this->product->expects($this->any())
            ->method('getSku')
            ->willReturn('sku');
        $this->product->expects($this->any())
            ->method('getName')
            ->willReturn('Name');
        $this->product->expects($this->any())
            ->method('getPrice')
            ->willReturn(1.0);
        $this->store->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('catalog/product/edit', ['id' => $this->product->getId(), 'store' => $storeId])
            ->willReturn($productUrl);
        $this->currency->expects($this->once())
            ->method('toCurrency')
            ->with(sprintf("%f", $this->product->getPrice()))
            ->willReturn('$10.00');

        $this->assertEquals($expectedValue, $this->productModifier->modifyData([$itemId => []]));
    }

    /**
     * @see ProductModifier::modifyData()
     */
    public function testModifyDataWithException(): void
    {
        $this->locator->expects($this->once())
            ->method('getCustomerPrice')
            ->willThrowException(new Exception('error'));
        $this->expectException(Exception::class);

        $this->productModifier->modifyData([]);
    }

    /**
     * @see ProductModifier::modifyMeta()
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testModifyMeta(): void
    {
        $expectedValue = [
            'general' => [
                'children' => [
                    'product_button' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentType' => 'container',
                                    'component' => 'EPuzzle_CustomerPriceAdminUi/js/form/components/chooser-button',
                                    'title' => 'Product',
                                    'require' => true,
                                    'requireMessage' => 'The product is required.',
                                    'externalProvider' => 'epuzzle_customer_prices_product_listing.'
                                        . 'epuzzle_customer_prices_product_listing_data_source',
                                    'scopeLabel' => '[global]',
                                    'links' => [
                                        'entityId' => '${ $.provider }:data.product_id',
                                        'options' => '${ $.provider }:data.product_options',
                                        '__disableTmpl' => [
                                            'options' => false,
                                            'entityId' => false
                                        ]
                                    ],
                                    'sortOrder' => 10
                                ]
                            ]
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
                                                    'targetName' => 'epuzzle_customer_price_form.'
                                                        . 'epuzzle_customer_price_form.general.product_model',
                                                    'actionName' => 'toggleModal'
                                                ],
                                                [
                                                    'targetName' => 'epuzzle_customer_price_form.'
                                                        . 'epuzzle_customer_price_form.general.product_model.'
                                                        . 'epuzzle_customer_prices_product_listing',
                                                    'actionName' => 'render'
                                                ]
                                            ],
                                            'title' => 'Assign product',
                                            'provider' => null
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'product_model' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentType' => 'modal',
                                    'dataScope' => '',
                                    'options' => [
                                        'title' => 'Assign product',
                                        'buttons' => [
                                            [
                                                'text' => 'Cancel',
                                                'actions' => [
                                                    'closeModal'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'children' => [
                            'epuzzle_customer_prices_product_listing' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'autoRender' => false,
                                            'componentType' => 'insertListing',
                                            'dataScope' => 'epuzzle_customer_prices_product_listing',
                                            'externalProvider' => 'epuzzle_customer_prices_product_listing.'
                                                . 'epuzzle_customer_prices_product_listing_data_source',
                                            'selectionsProvider' => 'epuzzle_customer_prices_product_listing.'
                                                . 'epuzzle_customer_prices_product_listing.product_columns.ids',
                                            'ns' => 'epuzzle_customer_prices_product_listing',
                                            'render_url' => 'https://example.com/admin/customerPrice',
                                            'realTimeLink' => true,
                                            'dataLinks' => [
                                                'imports' => false,
                                                'exports' => true
                                            ],
                                            'behaviourType' => 'simple',
                                            'externalFilterMode' => true,
                                            'imports' => [
                                                'productId' => '${ $.provider }:data.product_id',
                                                'storeId' => '${ $.provider }:data.store_id',
                                                'websiteId' => '${ $.provider }:data.website_id',
                                                '__disableTmpl' => [
                                                    'productId' => false,
                                                    "storeId" => false,
                                                    "websiteId" => false
                                                ]
                                            ],
                                            "exports" => [
                                                'productId' => '${ $.externalProvider }:params.product_id',
                                                'storeId' => '${ $.externalProvider }:params.store_id',
                                                'websiteId' => '${ $.externalProvider }:params.website_id',
                                                '__disableTmpl' => [
                                                    'productId' => false,
                                                    'storeId' => false,
                                                    'websiteId' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $renderUrl = 'https://example.com/admin/customerPrice';
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('mui/index/render')
            ->willReturn($renderUrl);

        $this->assertEquals($expectedValue, $this->productModifier->modifyMeta([]));
    }
}
