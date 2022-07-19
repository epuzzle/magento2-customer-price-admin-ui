<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Listing\CustomerPrice\Column;

use EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\CustomerPrice\Column\Price;
use Magento\Framework\Currency;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @see Price
 */
class PriceTest extends TestCase
{
    /**
     * @var Currency|MockObject
     */
    private Currency $currency;

    /**
     * @var Price
     */
    private Price $price;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $context = $this->createMock(ContextInterface::class);
        $uiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->currency = $this->createMock(Currency::class);
        $localeCurrency = $this->createMock(CurrencyInterface::class);
        $localeCurrency->expects($this->any())
            ->method('getCurrency')
            ->willReturn($this->currency);
        $storeManager = $this->createMock(StoreManagerInterface::class);

        $reflectionProperty = (new ReflectionClass(Price::class))
            ->getProperty('baseCurrencyCodes');
        $reflectionProperty->setAccessible(true);

        $this->price = new Price(
            $context,
            $uiComponentFactory,
            $localeCurrency,
            $storeManager,
            [],
            ['name' => 'price']
        );

        $reflectionProperty->setValue($this->price, [1 => 'USD']);
    }

    /**
     * @see Price::prepareDataSource()
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
                        'website_id' => 1,
                        'price' => '$10.00'
                    ],
                    [
                        'item_id' => 2,
                        'website_id' => 1,
                        'price' => '$20.00'
                    ]
                ]
            ]
        ];

        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'item_id' => 1,
                        'website_id' => 1,
                        'price' => 10.00,
                    ],
                    [
                        'item_id' => 2,
                        'website_id' => 1,
                        'price' => 20.00
                    ]
                ]
            ]
        ];

        $this->currency->expects($this->any())
            ->method('toCurrency')
            ->will(
                $this->returnValueMap([
                    [sprintf("%f", 10.00), [], '$10.00'],
                    [sprintf("%f", 20.00), [], '$20.00']
                ])
            );

        $this->assertEquals($expectedValue, $this->price->prepareDataSource($dataSource));
    }
}
