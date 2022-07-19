<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Model\CustomerPrice;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use EPuzzle\CustomerPrice\Api\Data\CustomerPriceInterfaceFactory;
use EPuzzle\CustomerPrice\Model\CustomerPrice;
use EPuzzle\CustomerPriceAdminUi\Model\Customer\GetCustomerByCustomerPriceId;
use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use EPuzzle\CustomerPriceAdminUi\Model\Product\GetProductByCustomerPriceId;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see Locator
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LocatorTest extends TestCase
{
    /**
     * @var CustomerPriceRepositoryInterface|MockObject
     */
    private CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * @var CustomerPriceInterfaceFactory|MockObject
     */
    private CustomerPriceInterfaceFactory $customerPriceFactory;

    /**
     * @var Http|MockObject
     */
    private Http $request;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var GetProductByCustomerPriceId|MockObject
     */
    private GetProductByCustomerPriceId $getProductByCustomerPriceId;

    /**
     * @var GetCustomerByCustomerPriceId|MockObject
     */
    private GetCustomerByCustomerPriceId $getCustomerByCustomerPriceId;

    /**
     * @var Product|MockObject
     */
    private Product $product;

    /**
     * @var Customer|MockObject
     */
    private Customer $customer;

    /**
     * @var Locator
     */
    private Locator $locator;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->customerPriceRepository = $this->createMock(CustomerPriceRepositoryInterface::class);
        $this->customerPriceFactory = $this->createMock(CustomerPriceInterfaceFactory::class);
        $this->request = $this->createMock(Http::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->getProductByCustomerPriceId = $this->createMock(GetProductByCustomerPriceId::class);
        $this->getCustomerByCustomerPriceId = $this->createMock(GetCustomerByCustomerPriceId::class);
        $this->product = $this->createMock(Product::class);
        $productFactory = $this->createMock(ProductInterfaceFactory::class);
        $productFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->product);
        $this->customer = $this->createMock(Customer::class);
        $customerFactory = $this->createMock(CustomerInterfaceFactory::class);
        $customerFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->customer);

        $this->locator = new Locator(
            $this->customerPriceRepository,
            $this->customerPriceFactory,
            $this->request,
            $this->storeManager,
            $this->getProductByCustomerPriceId,
            $this->getCustomerByCustomerPriceId,
            $productFactory,
            $customerFactory
        );
    }

    /**
     * @see Locator::getStore()
     */
    public function testGetStore(): void
    {
        $expectedValue = 1;
        $store = $this->createMock(StoreInterface::class);
        $store->expects($this->any())
            ->method('getId')
            ->willReturn($expectedValue);
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('store')
            ->willReturn($expectedValue);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->with($expectedValue)
            ->willReturn($store);

        $this->assertEquals($expectedValue, $this->locator->getStore()->getId());
    }

    /**
     * @see Locator::getStore()
     */
    public function testGetStoreResolveDefault(): void
    {
        $expectedValue = 1;
        $websiteId = 1;
        $customerPriceId = 1;
        $store = $this->createMock(StoreInterface::class);
        $store->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $store->expects($this->any())
            ->method('getId')
            ->willReturn($expectedValue);
        $customerPrice = $this->createMock(CustomerPrice::class);
        $customerPrice->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $this->customerPriceRepository->expects($this->once())
            ->method('get')
            ->with($customerPriceId)
            ->willReturn($customerPrice);
        $this->request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap([
                ['store', null, null],
                ['item_id', null, $customerPriceId]
            ]));
        $this->storeManager->expects($this->once())
            ->method('getStores')
            ->willReturn([$store]);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->with($expectedValue)
            ->willReturn($store);

        $this->assertEquals($expectedValue, $this->locator->getStore()->getId());
    }

    /**
     * @see Locator::getStore()
     */
    public function testGetStoreWithException(): void
    {
        $storeId = 1;
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('store')
            ->willReturn($storeId);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willThrowException(new NoSuchEntityException(__('error')));
        $this->expectException(NoSuchEntityException::class);
        $this->locator->getStore();
    }

    /**
     * @see Locator::getProduct()
     */
    public function testGetProduct(): void
    {
        $expectedValue = $this->product;
        $customerPriceId = 1;
        $storeId = 1;
        $customerPrice = $this->createMock(CustomerPrice::class);
        $customerPrice->expects($this->once())
            ->method('getItemId')
            ->willReturn($customerPriceId);
        $this->request->expects($this->any())
            ->method('getParam')
            ->will($this->returnValueMap([
                ['store', null, $storeId],
                ['item_id', null, $customerPriceId]
            ]));
        $store = $this->createMock(StoreInterface::class);
        $store->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($store);
        $this->customerPriceRepository->expects($this->once())
            ->method('get')
            ->with($customerPriceId)
            ->willReturn($customerPrice);
        $this->getProductByCustomerPriceId->expects($this->once())
            ->method('execute')
            ->willReturn($this->product);

        $this->assertEquals($expectedValue, $this->locator->getProduct());
    }

    /**
     * @see Locator::getCustomer()
     */
    public function testGetCustomer(): void
    {
        $expectedValue = $this->customer;
        $customerPriceId = 1;
        $customerPrice = $this->createMock(CustomerPrice::class);
        $customerPrice->expects($this->once())
            ->method('getItemId')
            ->willReturn($customerPriceId);
        $this->request->expects($this->any())
            ->method('getParam')
            ->with('item_id')
            ->willReturn($customerPriceId);
        $this->customerPriceRepository->expects($this->once())
            ->method('get')
            ->with($customerPriceId)
            ->willReturn($customerPrice);
        $this->getCustomerByCustomerPriceId->expects($this->once())
            ->method('execute')
            ->willReturn($this->customer);

        $this->assertEquals($expectedValue, $this->locator->getCustomer());
    }

    /**
     * @see Locator::getRequestStoreId()
     */
    public function testGetRequestStoreId(): void
    {
        $expectedValue = 1;
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('store')
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->locator->getRequestStoreId());
    }

    /**
     * @see Locator::getRequestStoreId()
     */
    public function testGetRequestStoreIdNull(): void
    {
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('store')
            ->willReturn(null);

        $this->assertNull($this->locator->getRequestStoreId());
    }
}
