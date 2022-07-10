<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPriceAction;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

/**
 * Render the grid of the customer prices
 */
class Index extends CustomerPriceAction implements HttpGetActionInterface
{
    /**
     * Render the grid of the customer prices
     *
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('EPuzzle_CustomerPriceAdminUi::customer_price');
        $resultPage->addBreadcrumb(__('Catalog'), __('Catalog'));
        $resultPage->addBreadcrumb(__('Inventory'), __('Inventory'));
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Prices'));
        return $resultPage;
    }
}
