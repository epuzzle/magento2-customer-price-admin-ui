<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice\Index;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see Index
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class IndexTest extends TestCase
{
    /**
     * @var Page|MockObject
     */
    private Page $resultPage;

    /**
     * @var Index
     */
    private Index $index;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $messageManager = $this->createMock(ManagerInterface::class);
        $request = $this->createMock(Http::class);
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
        $context = $this->createMock(Context::class);
        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_PAGE)
            ->willReturn($this->resultPage);
        $context->expects($this->any())
            ->method('getRequest')
            ->willReturn($request);
        $context->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($messageManager);
        $context->expects($this->any())
            ->method('getObjectManager')
            ->willReturn($this->objectManager);
        $context->expects($this->any())
            ->method('getResultRedirectFactory')
            ->willReturn($resultRedirectFactory);
        $context->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($resultFactory);

        $this->index = new Index(
            $context
        );
    }

    /**
     * @see Index::execute()
     */
    public function testExecute(): void
    {
        $this->assertSame($this->resultPage, $this->index->execute());
    }
}
