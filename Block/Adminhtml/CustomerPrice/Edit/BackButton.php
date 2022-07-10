<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Block\Adminhtml\CustomerPrice\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Get information about back (reset) button
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('*/*/');
    }
}
