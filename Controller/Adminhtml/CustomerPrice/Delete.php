<?php

declare(strict_types=1);

namespace EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPrice;

use EPuzzle\CustomerPrice\Api\CustomerPriceRepositoryInterface;
use EPuzzle\CustomerPriceAdminUi\Controller\Adminhtml\CustomerPriceAction;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Delete the customer price by ID
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Delete extends CustomerPriceAction implements HttpPostActionInterface
{
    /**
     * @var CustomerPriceRepositoryInterface
     */
    private CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * Delete
     *
     * @param Action\Context $context
     * @param CustomerPriceRepositoryInterface $customerPriceRepository
     */
    public function __construct(
        Action\Context $context,
        CustomerPriceRepositoryInterface $customerPriceRepository
    ) {
        parent::__construct($context);
        $this->customerPriceRepository = $customerPriceRepository;
    }

    /**
     * Delete the customer price by ID
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $itemId = (int)$this->getRequest()->getParam('item_id');
        if ($itemId) {
            try {
                $this->customerPriceRepository->deleteById($itemId);
                $this->messageManager->addSuccessMessage(__('You deleted the customer price.'));
                $resultRedirect->setPath('*/*/');
            } catch (Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
                $resultRedirect->setPath('*/*/edit', ['item_id' => $itemId]);
            }
            return $resultRedirect;
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a customer price to delete.'));
        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }
}
