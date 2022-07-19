<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Listing\CustomerPrice\Column;

use EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\CustomerPrice\Column\CustomerEmail;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see CustomerEmail
 */
class CustomerEmailTest extends TestCase
{
    /**
     * @var UrlInterface|MockObject
     */
    private UrlInterface $urlBuilder;

    /**
     * @var CustomerEmail
     */
    private CustomerEmail $customerEmail;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $context = $this->createMock(ContextInterface::class);
        $uiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->customerEmail = new CustomerEmail(
            $context,
            $uiComponentFactory,
            $this->urlBuilder,
            [],
            ['name' => 'customer_id']
        );
    }

    /**
     * @see CustomerEmail::prepareDataSource()
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
                        'customer_id' => '<a href="https://c.c/customer/index/edit/1" target="_blank">1</a>'
                    ],
                    [
                        'item_id' => 2,
                        'product_id' => 1,
                        'customer_id' => '<a href="https://c.c/customer/index/edit/12" target="_blank">2</a>'
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
                        'customer_id' => 2
                    ]
                ]
            ]
        ];

        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->will(
                $this->returnValueMap([
                    ['customer/index/edit', ['id' => 1], 'https://c.c/customer/index/edit/1'],
                    ['customer/index/edit', ['id' => 2], 'https://c.c/customer/index/edit/12']
                ])
            );

        $this->assertEquals($expectedValue, $this->customerEmail->prepareDataSource($dataSource));
    }
}
