<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Form\CustomerPrice\Modifier;

use EPuzzle\CustomerPrice\Model\CustomerPrice;
use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use EPuzzle\CustomerPriceAdminUi\Ui\Component\Form\CustomerPrice\Modifier\Customer as CustomerModifier;
use Exception;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see CustomerModifier
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CustomerTest extends TestCase
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
     * @var Customer|MockObject
     */
    private Customer $customer;

    /**
     * @var Locator|MockObject
     */
    private Locator $locator;

    /**
     * @var CustomerModifier
     */
    private CustomerModifier $customerModifier;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->customerPrice = $this->createMock(CustomerPrice::class);
        $this->customer = $this->createMock(Customer::class);
        $this->locator = $this->createMock(Locator::class);
        $this->locator->expects($this->any())
            ->method('getCustomerPrice')
            ->willReturn($this->customerPrice);
        $this->locator->expects($this->any())
            ->method('getCustomer')
            ->willReturn($this->customer);
        $localeCurrency = $this->createMock(CurrencyInterface::class);
        $this->customerModifier = new CustomerModifier(
            $this->urlBuilder,
            $this->locator,
            $localeCurrency
        );
    }

    /**
     * @see CustomerModifier::modifyData()
     */
    public function testModifyData(): void
    {
        $expectedValue = [
            1 => [
                'customer_id' => 1,
                'customer_options' => [
                    [
                        'label' => __('Email'),
                        'value' => '<a href="mailto:email@email.com" target="_blank">email@email.com</a>',
                    ],
                    [
                        'label' => __('Name'),
                        'value' => '<a href="https://example.com/admin/customerPrice"'
                            . ' target="_blank">Firstname Lastname</a>'
                    ]
                ]
            ]
        ];

        $itemId = 1;
        $customerId = 1;
        $customerUrl = 'https://example.com/admin/customerPrice';
        $this->customerPrice->expects($this->once())
            ->method('getItemId')
            ->willReturn($itemId);
        $this->customer->expects($this->any())
            ->method('getId')
            ->willReturn($customerId);
        $this->customer->expects($this->any())
            ->method('getEmail')
            ->willReturn('email@email.com');
        $this->customer->expects($this->any())
            ->method('getFirstname')
            ->willReturn('Firstname');
        $this->customer->expects($this->any())
            ->method('getLastname')
            ->willReturn('Lastname');
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('customer/index/edit', ['id' => $this->customer->getId()])
            ->willReturn($customerUrl);

        $this->assertEquals($expectedValue, $this->customerModifier->modifyData([$itemId => []]));
    }

    /**
     * @see CustomerModifier::modifyData()
     */
    public function testModifyDataWithException(): void
    {
        $this->locator->expects($this->once())
            ->method('getCustomerPrice')
            ->willThrowException(new Exception('error'));
        $this->expectException(Exception::class);

        $this->customerModifier->modifyData([]);
    }

    /**
     * @see CustomerModifier::modifyMeta()
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testModifyMeta(): void
    {
        $expectedValue = [
            'general' => [
                'children' => [
                    'customer_button' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentType' => 'container',
                                    'component' => 'EPuzzle_CustomerPriceAdminUi/js/form/components/chooser-button',
                                    'title' => __('Customer'),
                                    'require' => true,
                                    'requireMessage' => __('The customer is required.'),
                                    'externalProvider' => 'epuzzle_customer_prices_customer_listing.'
                                        . 'epuzzle_customer_prices_customer_listing_data_source',
                                    'scopeLabel' => '[global]',
                                    'links' => [
                                        'entityId' => '${ $.provider }:data.customer_id',
                                        'options' => '${ $.provider }:data.customer_options',
                                        '__disableTmpl' => [
                                            'options' => false,
                                            'entityId' => false,
                                        ],
                                    ],
                                    'sortOrder' => 20,
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
                                                    'targetName' => 'epuzzle_customer_price_form.'
                                                        . 'epuzzle_customer_price_form.general.customer_model',
                                                    'actionName' => 'toggleModal',
                                                ],
                                                [
                                                    'targetName' => 'epuzzle_customer_price_form.'
                                                        . 'epuzzle_customer_price_form.general.customer_model.'
                                                        . 'epuzzle_customer_prices_customer_listing',
                                                    'actionName' => 'render',
                                                ],
                                            ],
                                            'title' => __('Assign customer'),
                                            'provider' => null,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'customer_model' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentType' => 'modal',
                                    'dataScope' => '',
                                    'options' => [
                                        'title' => __('Assign customer'),
                                        'buttons' => [
                                            [
                                                'text' => __('Cancel'),
                                                'actions' =>
                                                    [
                                                        'closeModal',
                                                    ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'children' => [
                            'epuzzle_customer_prices_customer_listing' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'autoRender' => false,
                                            'componentType' => 'insertListing',
                                            'dataScope' => 'epuzzle_customer_prices_customer_listing',
                                            'externalProvider' => 'epuzzle_customer_prices_customer_listing.'
                                                . 'epuzzle_customer_prices_customer_listing_data_source',
                                            'selectionsProvider' => 'epuzzle_customer_prices_customer_listing.'
                                                . 'epuzzle_customer_prices_customer_listing.customer_columns.ids',
                                            'ns' => 'epuzzle_customer_prices_customer_listing',
                                            'render_url' => 'https://example.com/admin/customerPrice',
                                            'realTimeLink' => true,
                                            'dataLinks' => [
                                                'imports' => false,
                                                'exports' => true
                                            ],
                                            'behaviourType' => 'simple',
                                            'externalFilterMode' => true,
                                            'imports' => [
                                                'customerId' => '${ $.provider }:data.customer_id',
                                                'websiteId' => '${ $.provider }:data.website_id',
                                                '__disableTmpl' => [
                                                    'customerId' => false,
                                                    'websiteId' => false,
                                                ],
                                            ],
                                            'exports' => [
                                                'customerId' => '${ $.externalProvider }:params.customer_id',
                                                'websiteId' => '${ $.externalProvider }:params.website_id',
                                                '__disableTmpl' => [
                                                    'customerId' => false,
                                                    'websiteId' => false,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];
        $renderUrl = 'https://example.com/admin/customerPrice';
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('mui/index/render')
            ->willReturn($renderUrl);

        $this->assertEquals($expectedValue, $this->customerModifier->modifyMeta([]));
    }
}
