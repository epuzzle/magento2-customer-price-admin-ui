<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Block\Adminhtml\CustomerPrice\Edit;

use EPuzzle\CustomerPrice\Api\Data\CustomerPriceInterface;
use EPuzzle\CustomerPriceAdminUi\Block\Adminhtml\CustomerPrice\Edit\ResetButton;

/**
 * @see ResetButton
 */
class ResetButtonTest extends GenericButtonTest
{
    /**
     * @var ResetButton
     */
    private ResetButton $resetButton;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetButton = new ResetButton(
            $this->context,
            $this->customerPriceRepository
        );
    }

    /**
     * @see ResetButton::getButtonData()
     */
    public function testGetButtonData(): void
    {
        $buttonData = [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30,
        ];
        $customerPriceId = 3;
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
        $this->assertEquals($buttonData, $this->resetButton->getButtonData());
    }
}
