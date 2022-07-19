<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Listing\Product\Column;

use EPuzzle\CustomerPriceAdminUi\Ui\Component\Listing\Product\Column\Price;
use Exception;
use Magento\Framework\Currency;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
     * @var StoreManagerInterface|MockObject
     */
    private StoreManagerInterface $storeManager;

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
        $this->store = $this->createMock(Store::class);
        $this->store->expects($this->any())
            ->method('getId')
            ->willReturn(Store::DEFAULT_STORE_ID);
        $this->store->expects($this->any())
            ->method('getBaseCurrencyCode')
            ->willReturn('USD');
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->with(Store::DEFAULT_STORE_ID)
            ->willReturn($this->store);

        $this->price = new Price(
            $context,
            $uiComponentFactory,
            $localeCurrency,
            $this->storeManager,
            [],
            ['name' => 'price']
        );
    }

    /**
     * @see Price::prepareDataSource()
     */
    public function testPrepareDataSource(): void
    {
        $expectedValue = [
            'data' => [
                'items' => [
                    ['price' => '$10.00'],
                    ['price' => '$20.00']
                ]
            ]
        ];

        $dataSource = [
            'data' => [
                'items' => [
                    ['price' => 10.00],
                    ['price' => 20.00]
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

    /**
     * @see Price::prepareDataSource()
     */
    public function testPrepareDataSourceWithException(): void
    {
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willThrowException(new Exception('error'));
        $this->expectException(Exception::class);

        $this->price->prepareDataSource(['data' => ['items' => []]]);
    }
}
