<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Block\Adminhtml\CustomerPrice\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ResetButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->getItemId()) {
            $data = [
                'label' => __('Reset'),
                'class' => 'reset',
                'on_click' => 'location.reload();',
                'sort_order' => 30,
            ];
        }
        return $data;
    }
}
