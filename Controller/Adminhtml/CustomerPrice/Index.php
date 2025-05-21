<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPriceAction;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

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
        $resultPage->addBreadcrumb((string)__('Catalog'), (string)__('Catalog'));
        $resultPage->addBreadcrumb((string)__('Inventory'), (string)__('Inventory'));
        $resultPage->getConfig()->getTitle()->prepend((string)__('Customer Prices'));
        return $resultPage;
    }
}
