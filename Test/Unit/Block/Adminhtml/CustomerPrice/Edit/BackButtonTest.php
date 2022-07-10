<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Test\Unit\Block\Adminhtml\CustomerPrice\Edit;

use EPuzzle\CustomerPriceAdminUi\Block\Adminhtml\CustomerPrice\Edit\BackButton;

/**
 * @see BackButton
 */
class BackButtonTest extends GenericButtonTest
{
    /**
     * @var BackButton
     */
    private BackButton $backButton;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->backButton = new BackButton(
            $this->context,
            $this->customerPriceRepository
        );
    }

    /**
     * @see BackButton::getButtonData()
     */
    public function testGetButtonData(): void
    {
        $backUrl = 'https://example.com/admin/customerPrice';
        $buttonData = [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $backUrl),
            'class' => 'back',
            'sort_order' => 10
        ];

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->willReturn($backUrl);
        $this->assertEquals($buttonData, $this->backButton->getButtonData());
    }
}
