<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPriceAction;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Forward to the edit page
 */
class NewAction extends CustomerPriceAction implements HttpGetActionInterface
{
    /**
     * Forward to the edit page
     *
     * @return Forward
     */
    public function execute(): Forward
    {
        /** @var Forward $forwardResult */
        $forwardResult = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $forwardResult->forward('edit');
        return $forwardResult;
    }
}
