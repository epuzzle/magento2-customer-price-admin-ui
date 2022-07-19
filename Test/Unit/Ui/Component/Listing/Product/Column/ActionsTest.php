<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Listing\Product\Column;

use EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\Product\Column\Actions;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see Actions
 */
class ActionsTest extends TestCase
{
    /**
     * @var UrlInterface|MockObject
     */
    private UrlInterface $urlBuilder;

    /**
     * @var Actions
     */
    private Actions $actions;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $context = $this->createMock(ContextInterface::class);
        $uiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->actions = new Actions(
            $context,
            $uiComponentFactory,
            $this->urlBuilder,
            [],
            ['name' => 'actions']
        );
    }

    /**
     * @see Actions::prepareDataSource()
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testPrepareDataSource(): void
    {
        $expectedValue = [
            'data' => [
                'items' => [
                    [
                        'entity_id' => 1,
                        'name' => 'Name1',
                        'sku' => 'sku1',
                        'price' => '$10.00',
                        'store_id' => 1,
                        'actions' => [
                            'edit' => [
                                'callback' => [
                                    [
                                        'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.'
                                            . 'general.product_model',
                                        'target' => 'closeModal'
                                    ],
                                    [
                                        'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.'
                                            . 'general.product_button',
                                        'target' => 'updateData',
                                        'params' => [
                                            'entityId' => 1,
                                            'options' => [
                                                [
                                                    'label' => 'SKU',
                                                    'value' => 'sku1'
                                                ],
                                                [
                                                    'label' => 'Name',
                                                    'value' => '<a href="" target="_blank">Name1</a>'
                                                ],
                                                [
                                                    "label" => 'Price',
                                                    'value' => '$10.00'
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'href' => '#',
                                'label' => 'Assign',
                                '__disableTmpl' => true
                            ]
                        ]
                    ],
                    [
                        'entity_id' => 2,
                        'name' => 'Name2',
                        'sku' => 'sku2',
                        'price' => '$20.00',
                        'store_id' => 1,
                        'actions' => [
                            'edit' => [
                                'callback' => [
                                    [
                                        'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.'
                                            . 'general.product_model',
                                        'target' => 'closeModal'
                                    ],
                                    [
                                        'provider' => 'epuzzle_customer_price_form.epuzzle_customer_price_form.'
                                            . 'general.product_button',
                                        'target' => 'updateData',
                                        'params' => [
                                            'entityId' => 2,
                                            'options' => [
                                                [
                                                    'label' => 'SKU',
                                                    'value' => 'sku2'
                                                ],
                                                [
                                                    'label' => 'Name',
                                                    'value' => '<a href="" target="_blank">Name2</a>'
                                                ],
                                                [
                                                    'label' => 'Price',
                                                    'value' => '$20.00'
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'href' => '#',
                                'label' => 'Assign',
                                '__disableTmpl' => true
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'entity_id' => 1,
                        'name' => 'Name1',
                        'sku' => 'sku1',
                        'price' => '$10.00',
                        'store_id' => 1
                    ],
                    [
                        'entity_id' => 2,
                        'name' => 'Name2',
                        'sku' => 'sku2',
                        'price' => '$20.00',
                        'store_id' => 1
                    ]
                ]
            ]
        ];

        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will(
                $this->returnValueMap([
                    ['catalog/product/edit', ['id' => 1, 'store_id' => 1], 'https://example.com/admin/customerPrice/1'],
                    ['catalog/product/edit', ['id' => 2, 'store_id' => 1], 'https://example.com/admin/customerPrice/2']
                ])
            );

        $this->assertEquals($expectedValue, $this->actions->prepareDataSource($dataSource));
    }
}
