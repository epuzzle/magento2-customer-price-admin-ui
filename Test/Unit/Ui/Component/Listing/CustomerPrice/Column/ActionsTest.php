<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Listing\CustomerPrice\Column;

use EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\CustomerPrice\Column\Actions;
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
                        'item_id' => 1,
                        'product_id' => 1,
                        'customer_id' => 1,
                        'actions' => [
                            'edit' => [
                                'href' => 'https://c.c/epuzzle/customerPrice/edit/1',
                                'label' => 'Edit',
                                '__disableTmpl' => true
                            ],
                            'delete' => [
                                'href' => 'https://c.c/epuzzle/customerPrice/delete/1',
                                'label' => 'Delete',
                                'confirm' => [
                                    'title' => 'Delete 1',
                                    'message' => 'Are you sure you want to delete a 1 record?',
                                    '__disableTmpl' => true
                                ],
                                'post' => true,
                                '__disableTmpl' => true
                            ],
                            'to_product' => [
                                'href' => 'https://c.c/catalog/product/edit/1',
                                'label' => 'Go to Product',
                                '__disableTmpl' => true
                            ],
                            'to_customer' => [
                                'href' => 'https://c.c/customer/index/edit/1',
                                'label' => 'Go to Customer',
                                '__disableTmpl' => true
                            ]
                        ]
                    ],
                    [
                        'item_id' => 2,
                        'product_id' => 1,
                        'customer_id' => 1,
                        'actions' => [
                            'edit' => [
                                'href' => 'https://c.c/epuzzle/customerPrice/edit/2',
                                'label' => 'Edit',
                                '__disableTmpl' => true
                            ],
                            'delete' => [
                                'href' => 'https://c.c/epuzzle/customerPrice/delete/2',
                                'label' => 'Delete',
                                'confirm' => [
                                    'title' => 'Delete 2',
                                    'message' => 'Are you sure you want to delete a 2 record?',
                                    '__disableTmpl' => true
                                ],
                                'post' => true,
                                '__disableTmpl' => true
                            ],
                            'to_product' => [
                                'href' => 'https://c.c/catalog/product/edit/1',
                                'label' => 'Go to Product',
                                '__disableTmpl' => true
                            ],
                            'to_customer' => [
                                'href' => 'https://c.c/customer/index/edit/1',
                                'label' => 'Go to Customer',
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
                        'item_id' => 1,
                        'product_id' => 1,
                        'customer_id' => 1,
                    ],
                    [
                        'item_id' => 2,
                        'product_id' => 1,
                        'customer_id' => 1
                    ]
                ]
            ]
        ];

        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will(
                $this->returnValueMap([
                    ['epuzzle/customerPrice/edit', ['item_id' => 1], 'https://c.c/epuzzle/customerPrice/edit/1'],
                    ['epuzzle/customerPrice/delete', ['item_id' => 1], 'https://c.c/epuzzle/customerPrice/delete/1'],
                    ['epuzzle/customerPrice/edit', ['item_id' => 2], 'https://c.c/epuzzle/customerPrice/edit/2'],
                    ['epuzzle/customerPrice/delete', ['item_id' => 2], 'https://c.c/epuzzle/customerPrice/delete/2'],
                    ['catalog/product/edit', ['id' => 1], 'https://c.c/catalog/product/edit/1'],
                    ['customer/index/edit', ['id' => 1], 'https://c.c/customer/index/edit/1']
                ])
            );

        $this->assertEquals($expectedValue, $this->actions->prepareDataSource($dataSource));
    }
}
