<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice\NewAction;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManager\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see NewAction
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NewActionTest extends TestCase
{
    /**
     * @var Forward|MockObject
     */
    private Forward $resultForward;

    /**
     * @var NewAction
     */
    private NewAction $newAction;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $messageManager = $this->createMock(ManagerInterface::class);
        $request = $this->createMock(Http::class);
        $this->resultForward = $this->createMock(Forward::class);
        $resultRedirectFactory = $this->createMock(RedirectFactory::class);
        $context = $this->createMock(Context::class);
        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_FORWARD)
            ->willReturn($this->resultForward);
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

        $this->newAction = new NewAction(
            $context
        );
    }

    /**
     * @see NewAction::execute()
     */
    public function testExecute(): void
    {
        $this->assertSame($this->resultForward, $this->newAction->execute());
    }
}
