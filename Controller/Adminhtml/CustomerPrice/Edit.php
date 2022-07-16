<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPriceAction;
use EPuzzle\CustomerPriceAdminUi\Model\CustomerPrice\Locator;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Edit the customer price entity
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Edit extends CustomerPriceAction implements HttpGetActionInterface
{
    /**
     * @var Locator
     */
    private Locator $locator;

    /**
     * Edit
     *
     * @param Action\Context $context
     * @param Locator $locator
     */
    public function __construct(
        Action\Context $context,
        Locator $locator
    ) {
        parent::__construct($context);
        $this->locator = $locator;
    }

    /**
     * Edit the customer price entity
     *
     * @return ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->getParam('store')) {
            return $resultRedirect->setUrl(
                $this->getUrl(
                    '*/*/*/',
                    [
                        'store' => $this->locator->getStore()->getId(),
                        '_current' => true
                    ]
                )
            );
        }

        $this->getMessageManager()->addNoticeMessage(
            __(
                'Please note that the customer price is based on scope. The selected scope is "%scope".',
                ['scope' => $this->locator->getStore()->getName()]
            )
        );

        $customerPrice = $this->locator->getCustomerPrice();
        if (!empty($this->getRequest()->getParam('item_id')) && !$customerPrice->getItemId()) {
            $this->messageManager->addErrorMessage(__('This customer price no longer exists.'));
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        $pageTitle = $customerPrice->getItemId() ? __('Edit') : __('New');
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('EPuzzle_CustomerPriceAdminUi::customer_price');
        $resultPage->addBreadcrumb(__('Catalog'), __('Catalog'));
        $resultPage->addBreadcrumb(__('Inventory'), __('Inventory'));
        $resultPage->addBreadcrumb($pageTitle, $pageTitle);
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Prices'));
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
