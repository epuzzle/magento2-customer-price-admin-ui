<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Ui\Component\Form\CustomerPrice\Modifier;

use EPuzzle\CustomerPrice\Model\CustomerPrice;
use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use EPuzzle\CustomerPriceAdminUi\Ui\Component\Form\CustomerPrice\Modifier\General as GeneralModifier;
use Exception;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see GeneralModifier
 */
class GeneralTest extends TestCase
{
    /**
     * @var CustomerPrice|MockObject
     */
    private CustomerPrice $customerPrice;

    /**
     * @var Store|MockObject
     */
    private Store $store;

    /**
     * @var Locator|MockObject
     */
    private Locator $locator;

    /**
     * @var GeneralModifier
     */
    private GeneralModifier $generalModifier;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->customerPrice = $this->createMock(CustomerPrice::class);
        $currency = $this->createMock(Currency::class);
        $currency->expects($this->any())
            ->method('getCurrencySymbol')
            ->willReturn('USD');
        $this->store = $this->createMock(Store::class);
        $this->store->expects($this->any())
            ->method('getBaseCurrency')
            ->willReturn($currency);
        $this->locator = $this->createMock(Locator::class);
        $this->locator->expects($this->any())
            ->method('getCustomerPrice')
            ->willReturn($this->customerPrice);
        $this->locator->expects($this->any())
            ->method('getStore')
            ->willReturn($this->store);
        $this->generalModifier = new GeneralModifier(
            $this->locator
        );
    }

    /**
     * @see GeneralModifier::modifyData()
     */
    public function testModifyData(): void
    {
        $expectedValue = [
            1 => [
                'currency' => 'USD',
                'store_id' => 1,
                'website_id' => 1,
            ],
        ];

        $itemId = 1;
        $storeId = 1;
        $this->locator->expects($this->once())
            ->method('getRequestStoreId')
            ->willReturn($storeId);
        $this->customerPrice->expects($this->once())
            ->method('getItemId')
            ->willReturn($itemId);
        $this->customerPrice->expects($this->any())
            ->method('getData')
            ->willReturn([]);
        $this->store->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);
        $this->store->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($storeId);

        $this->assertEquals($expectedValue, $this->generalModifier->modifyData([$itemId => []]));
    }

    /**
     * @see GeneralModifier::modifyData()
     */
    public function testModifyDataWithException(): void
    {
        $this->locator->expects($this->once())
            ->method('getCustomerPrice')
            ->willThrowException(new Exception('error'));
        $this->expectException(Exception::class);

        $this->generalModifier->modifyData([]);
    }

    /**
     * @see GeneralModifier::modifyMeta()
     */
    public function testModifyMeta(): void
    {
        $expectedValue = [];
        $this->assertEquals($expectedValue, $this->generalModifier->modifyMeta([]));
    }
}
