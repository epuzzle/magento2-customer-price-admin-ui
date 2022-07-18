<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use EPuzzle\CustomerPrice\Model\CustomerPrice;
use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice\Save;
use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Store\Api\Data\WebsiteInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see Save
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveTest extends TestCase
{
    /**
     * @var Redirect|MockObject
     */
    private Redirect $resultRedirect;

    /**
     * @var ManagerInterface|MockObject
     */
    private ManagerInterface $messageManager;

    /**
     * @var RequestInterface|MockObject
     */
    private RequestInterface $request;

    /**
     * @var CustomerPriceRepositoryInterface|MockObject
     */
    private CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * @var CustomerPrice|MockObject
     */
    private CustomerPrice $customerPrice;

    /**
     * @var Locator|MockObject
     */
    private Locator $locator;

    /**
     * @var Save
     */
    private Save $save;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->request = $this->createMock(Http::class);
        $this->request->expects($this->any())
            ->method('getPostValue')
            ->willReturn([
                'product_id' => 1,
                'customer_id' => 1,
                'price' => 10.0,
                'qty' => 1.0
            ]);
        $this->resultRedirect = $this->createMock(Redirect::class);
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultRedirect);
        $context = $this->createMock(Context::class);
        $context->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->request);
        $context->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManager);
        $context->expects($this->any())
            ->method('getObjectManager')
            ->willReturn($this->objectManager);
        $context->expects($this->any())
            ->method('getResultRedirectFactory')
            ->willReturn($resultRedirectFactory);
        $this->customerPriceRepository = $this->createMock(CustomerPriceRepositoryInterface::class);
        $this->customerPrice = $this->createMock(CustomerPrice::class);
        $this->website = $this->createMock(WebsiteInterface::class);
        $this->website->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $this->locator = $this->createMock(Locator::class);
        $this->locator->expects($this->any())
            ->method('getCustomerPrice')
            ->willReturn($this->customerPrice);
        $this->locator->expects($this->any())
            ->method('getWebsite')
            ->willReturn($this->website);

        $this->save = new Save(
            $context,
            $this->locator,
            $this->customerPriceRepository
        );
    }

    /**
     * @see Delete::execute()
     */
    public function testExecute()
    {
        $itemId = 1;
        $this->customerPrice->expects($this->any())
            ->method('getItemId')
            ->willReturn($itemId);
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('item_id')
            ->willReturn($itemId);
        $this->messageManager->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You saved the customer price.'));
        $this->messageManager->expects($this->never())
            ->method('addErrorMessage');
        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->save->execute());
    }

    /**
     * @see Save::execute()
     */
    public function testExecuteNotFound()
    {
        $this->customerPrice->expects($this->any())
            ->method('getItemId')
            ->willReturn(0);
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('item_id')
            ->willReturn(1);
        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('This customer price no longer exists.'));
        $this->messageManager->expects($this->never())
            ->method('addSuccessMessage');
        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->save->execute());
    }

    /**
     * @see Save::execute()
     */
    public function testExecuteWithException()
    {
        $itemId = 1;
        $this->customerPrice->expects($this->any())
            ->method('getItemId')
            ->willReturn($itemId);
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('item_id')
            ->willReturn($itemId);
        $this->customerPriceRepository->expects($this->once())
            ->method('save')
            ->willThrowException(new CouldNotSaveException(__('error')));
        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('error'));
        $this->messageManager->expects($this->never())
            ->method('addSuccessMessage');
        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/edit')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->save->execute());
    }
}
