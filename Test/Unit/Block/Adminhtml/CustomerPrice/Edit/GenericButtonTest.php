<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Block\Adminhtml\CustomerPrice\Edit;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use EPuzzle\CustomerPrice\Api\Data\CustomerPriceInterface;
use EPuzzle\CustomerPriceAdminUi\Block\Adminhtml\CustomerPrice\Edit\GenericButton;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see GenericButton
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GenericButtonTest extends TestCase
{
    /**
     * @var UrlInterface|MockObject
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var RequestInterface|MockObject
     */
    protected RequestInterface $request;

    /**
     * @var Context|MockObject
     */
    protected Context $context;

    /**
     * @var CustomerPriceRepositoryInterface|MockObject
     */
    protected CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * @var GenericButton
     */
    private GenericButton $genericButton;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->context = $this->createMock(Context::class);
        $this->context->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilder);
        $this->context->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->customerPriceRepository = $this->createMock(CustomerPriceRepositoryInterface::class);
        $this->genericButton = new GenericButton($this->context, $this->customerPriceRepository);
    }

    /**
     * @see GenericButton::getItemId()
     */
    public function testGetItemId(): void
    {
        $customerPriceId = 1;
        $customerPrice = $this->createMock(CustomerPriceInterface::class);
        $customerPrice->expects($this->any())
            ->method('getItemId')
            ->willReturn($customerPriceId);
        $this->request->expects($this->any())
            ->method('getParam')
            ->with('item_id')
            ->willReturn($customerPriceId);
        $this->customerPriceRepository->expects($this->any())
            ->method('get')
            ->with($customerPriceId)
            ->willReturn($customerPrice);
        $this->assertEquals($customerPriceId, $this->genericButton->getItemId());
    }

    /**
     * @see GenericButton::getUrl()
     */
    public function testGetUrl(): void
    {
        $expectedValue = 'https://example.com/admin/customerPrice';
        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->willReturn($expectedValue);
        $this->assertEquals($expectedValue, $this->genericButton->getUrl());
    }
}
