<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use EPuzzle\CustomerPrice\Model\CustomerPrice;
use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice\Edit;
use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Store\Api\Data\WebsiteInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see Edit
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditTest extends TestCase
{
    /**
     * @var Redirect|MockObject
     */
    private Redirect $resultRedirect;

    /**
     * @var Page|MockObject
     */
    private Page $resultPage;

    /**
     * @var Data|MockObject
     */
    private Data $backendHelper;

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
     * @var Edit
     */
    private Edit $edit;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->request = $this->createMock(Http::class);
        $this->resultRedirect = $this->createMock(Redirect::class);
        $pageTitle = $this->createMock(Title::class);
        $pageConfig = $this->createMock(Config::class);
        $pageConfig->expects($this->any())
            ->method('getTitle')
            ->willReturn($pageTitle);
        $this->resultPage = $this->createMock(Page::class);
        $this->resultPage->expects($this->any())
            ->method('getConfig')
            ->willReturn($pageConfig);
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $resultRedirectFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->resultRedirect);
        $this->backendHelper = $this->createMock(Data::class);
        $context = $this->createMock(Context::class);
        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultPage);
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
        $context->expects($this->any())
            ->method('getHelper')
            ->willReturn($this->backendHelper);
        $context->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($resultFactory);
        $this->customerPriceRepository = $this->createMock(CustomerPriceRepositoryInterface::class);
        $this->customerPrice = $this->createMock(CustomerPrice::class);
        $this->website = $this->createMock(WebsiteInterface::class);
        $this->website->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $locator = $this->createMock(Locator::class);
        $locator->expects($this->any())
            ->method('getCustomerPrice')
            ->willReturn($this->customerPrice);
        $locator->expects($this->any())
            ->method('getWebsite')
            ->willReturn($this->website);

        $this->edit = new Edit(
            $context,
            $locator
        );
    }

    /**
     * @see Edit::execute()
     */
    public function testExecute(): void
    {
        $itemId = 1;
        $storeId = 1;
        $this->customerPrice->expects($this->any())
            ->method('getItemId')
            ->willReturn($itemId);
        $this->request->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap([
                    ['store', null, $storeId],
                    ['item_id', null, $itemId]
                ])
            );

        $this->assertSame($this->resultPage, $this->edit->execute());
    }

    /**
     * @see Edit::execute()
     */
    public function testExecuteWithoutStore(): void
    {
        $url = 'https://m2.com/admin/customerPrice/edit/store/1';
        $this->request->expects($this->once())
            ->method('getParam')
            ->with('store')
            ->willReturn(null);
        $this->backendHelper->expects($this->once())
            ->method('getUrl')
            ->willReturn($url);
        $this->resultRedirect->expects($this->once())
            ->method('setUrl')
            ->with($url)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->edit->execute());
    }

    /**
     * @see Edit::execute()
     */
    public function testExecuteNotFound(): void
    {
        $itemId = 1;
        $storeId = 1;
        $this->customerPrice->expects($this->once())
            ->method('getItemId')
            ->willReturn(0);
        $this->request->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap([
                    ['store', null, $storeId],
                    ['item_id', null, $itemId]
                ])
            );
        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('This customer price no longer exists.'));
        $this->messageManager->expects($this->never())
            ->method('addSuccessMessage');
        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirect, $this->edit->execute());
    }
}
