<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice\Delete;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManager\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see Delete
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class DeleteTest extends TestCase
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
     * @var Delete
     */
    private Delete $delete;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->request = $this->createMock(Http::class);
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

        $this->delete = new Delete(
            $context,
            $this->customerPriceRepository
        );
    }

    /**
     * @see Delete::execute()
     */
    public function testExecute()
    {
        $itemId = 1;
        $this->request->expects($this->once())
            ->method('getParam')
            ->willReturn($itemId);
        $this->messageManager->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You deleted the customer price.'));
        $this->messageManager->expects($this->never())
            ->method('addErrorMessage');
        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->delete->execute());
    }

    /**
     * @see Delete::execute()
     */
    public function testExecuteNotFound()
    {
        $this->request->expects($this->once())
            ->method('getParam')
            ->willReturn(null);
        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('We can\'t find a customer price to delete.'));
        $this->messageManager->expects($this->never())
            ->method('addSuccessMessage');
        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->delete->execute());
    }

    /**
     * @see Delete::execute()
     */
    public function testExecuteWithException()
    {
        $itemId = 1;
        $this->request->expects($this->once())
            ->method('getParam')
            ->willReturn($itemId);
        $this->customerPriceRepository->expects($this->once())
            ->method('deleteById')
            ->willThrowException(new CouldNotDeleteException(__('error')));
        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('error'));
        $this->messageManager->expects($this->never())
            ->method('addSuccessMessage');
        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/edit')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->delete->execute());
    }
}
